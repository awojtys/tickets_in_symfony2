<?php
namespace Awojtys\TicketServBundle\DependencyInjection;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use \Imagick;

class ImageOperations extends Controller
{
    protected $_em;
    protected $_config;
    protected $_max_size;
    protected $_image_files;
    
    public function __construct(\Doctrine\ORM\EntityManager $em, $config)
    {
        $this->_em = $em;
        $this->_config = $config;
        
    }
    
    public function validateImage($file, $id)
    {
        try
        {
            if(strstr($file->getmimeType(), 'image/'))
            {
                $filename = $this->_imageRename($file, $id);
                return $info = array('type' => true, 'message' => 'Avatar ustawiony!', 'filename' => $filename);
            }
            else
            {
                return $info = array('type' => false, 'message' => 'Zły format plików!');
            }
        }
        catch(\Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException $e)
        {
            return $info = array('type' => false, 'message' => 'Plik jest za duży. Ograniczenie do 8 megabajtów!');
        }
        
    }
    
    protected function _getMaxSize()
    {
        $config = $this->_config->getAllOptions();
        $width = $config['Avatar_Width'];
        $height = $config['Avatar_Height'];
        
        return $this->_max_size = array('width' => $config['Avatar_Width'], 'height' => $config['Avatar_Height']);
    }
    
    protected function _imageRename($file, $id)
    {
        $extension = pathinfo($file->getmimeType());
        $file->move($_SERVER['DOCUMENT_ROOT'] . '/avatars/original/', $id . '.' . $extension['filename']);
        
        return $filename = $id . '.' . $extension['filename'];
    }
    
    public function getRename($file, $id)
    {
        $this->_getMaxSize();
        
        $extension = pathinfo($file);
        if($extension['filename'] != 'none')
        {
            return '/avatars/'. $id . '.' . $this->_max_size['width'] . 'x' . $this->_max_size['height'] . '.' . $extension['extension'];
        }
        else
        {
            return '/avatars/none.png';
        }
    }
    
    public function imageResize($file)
    {
        $expoded_file = explode('.', $file);
        $all_size = $this->_mergeSize($expoded_file[0]);
        $extension = explode('.', $file);
        
        $filepath = $_SERVER['DOCUMENT_ROOT'] . '/avatars/original/' . $file;
        $filearray = pathinfo($filepath);   
        
        $imagick = new Imagick($filepath);
        
        foreach ($all_size as $size)
        {
            $imagick ->readimage($filepath);
            $geometry = $imagick ->getimagegeometry();
            $width = $geometry['width'];
            $height = $geometry['height'];
            
            $imagick->resizeImage($size['width'], $size['height'],Imagick::FILTER_LANCZOS,1, true);
            
            if($extension[0] == 'none')
            {
                $imagick ->writeimage($filearray['dirname'] . '/../' . $filearray['basename']);
            }
            else
            {
                $imagick ->writeimage($filearray['dirname'] . '/../' . $filearray['filename'] . '.' . $size['width'] . 'x' . $size['height'] . '.' . $filearray['extension']);
            }
        }
        
        $imagick ->clear();
        $imagick ->destroy();
    }
    
    protected function _getAllSizes($id)
    {
        $images = array();
        $katalog= $_SERVER['DOCUMENT_ROOT'] . '/avatars/'; //przypisanie do zmiennej jakiegos katalogu z plikami do wyswietlenia
        $dir=opendir($katalog); //otwarcie katalogu i przypisanie do zmiennej
        while($nazwa_pliku=readdir($dir)) //petla przypisujaca do zmiennej zawartosc katalogu
        {
            if(strstr($nazwa_pliku, $id .'.')) //pomijaj plikow “.” i “..”
            {
                $images[] = $nazwa_pliku;
            }
        }
        closedir($dir); //zamkniecie katalogu z plikami do wyswieltenia
        
        return $this->_image_files = $images;
    }
    protected function _returnSize($id)
    {
        $image_size = array();
        foreach ($this->_getAllSizes($id) as $value)
        {
            $name = explode('.', $value);
            $name2 = explode('x', $name[1]);
            $image_size[] = array('width' => $name2[0], 'height' => $name2[1]);
        }
        return $this->_all_size = $image_size;
    }
    
    protected function _mergeSize($id)
    {
        $max_size = $this->_getMaxSize();
        if($id != 'none')
        {
            $size = $this->_returnSize($id);
        }
        
        $size[] = array('width' => $max_size['width'], 'height' => $max_size['height']);
        
        return $size;
    }
    
    public function removeImages($id)
    {
        $filespath = $_SERVER['DOCUMENT_ROOT'] . '/avatars/';
        @unlink($filespath . $id . '.png');
        @unlink($filespath . $id . '.jpg');
        @unlink($filespath . $id . '.gif');
        @unlink($filespath . $id . '.jpeg');
        
        
        $files = $this->_getAllSizes($id);
       
        foreach($files as $file)
        {
            unlink($filespath . $file);
        }
        
        $user = $this->_em->getRepository('AwojtysTicketServBundle:User')->find($id);
        $user->setAvatar('none.png');
        $this->_em->flush();
        
    }
    
}

?>
