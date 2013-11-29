<?php
namespace Awojtys\TicketServBundle\DependencyInjection;

use Awojtys\TicketServBundle\Entity\Config;

class ConfigHelper
{
    protected $_em;
    protected $_config;
    protected $_password;
    protected $_temp;
    protected $_decryptedPassword;


    public function __construct(\Doctrine\ORM\EntityManager $em)
    {
        $this->_em = $em;
    }
    
    public function getAllOptions()
    {
        $options = $this->_em->getRepository('AwojtysTicketServBundle:Config')->findAll();
        $data = array();
        foreach ($options as $value)
        {
            if(strstr($value->getName(), 'Password'))
            {
                $data[$value->getName()] = (trim($this->decryptPassword($value->getValue())));
            }
            else {
            $data[$value->getName()] = $value->getValue();            
            }

        }
        return $data;
        
    }
    
    public function getOneOption($name)
    {
        $result = $this->_em->getRepository('AwojtysTicketServBundle:Config')->findBy(array('name' => $name));
        return $result;
    }
    
    public function existOption($name)
    {
        $result = $this->_em->getRepository('AwojtysTicketServBundle:Config')->findBy(array('name' => $name));
        if($result != null)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    public function encryptPassword($password)
    {
        $this->_password = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, 'grre%tGRG!@#!xnew*&32!', $password, MCRYPT_MODE_ECB);

        return base64_encode($this->_password);
    }
    
    public function decryptPassword($password)
    {
        if ($this->_decryptedPassword == null) {
            $password = base64_decode($password);
            $this->_decryptedPassword = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, 'grre%tGRG!@#!xnew*&32!', $password, MCRYPT_MODE_ECB);
        }
        return $this->_decryptedPassword;
    }
    
    protected function _setTemp()
    {
        $values = $this->getAllOptions();
        return $this->_temp = $values;
    }
    
    protected function _prepareOptions()
    {
        $this->_setTemp();
        $values = $this->getAllOptions();

        $config = array(
            '1' => array(
                'Name' => 'Host_Name',
                'Value' => !array_key_exists('Host_Name', $values) ? 'smtp.gmail.com' : $values['Host_Name'],
                'Type' => 'text',
                'Label' => 'Ustaw adres SMTP serwera pocztowego',
                'Required' => true
            ),
            '2' => array(
                'Name' => 'Set_Encryption',
                'Value' => !array_key_exists('Set_Encryption', $values) ? array('tls' => ' tls', 'ssl' => 'ssl') : $values['Set_Encryption'] == 'tls' ? array('tls' => ' tls', 'ssl' => 'ssl') : array('ssl' => ' ssl', 'tls' => 'tls'),
                'Type' => 'choice',
                'Label' => 'Wybierz sposób połączenia',
                'Required' => true
            ),
            '3' => array(
                'Name' => 'Set_Port',
                'Value' => !array_key_exists('Set_Port', $values) ? '465' : $values['Set_Port'],
                'Type' => 'text',
                'Label' => 'Podaj port dla SMTP serwera pocztowego',
                'Required' => true
            ),
            '4' => array(
                'Name' => 'Set_Mail_Username',
                'Value' => !array_key_exists('Set_Mail_Username', $values) ? 'default' : $values['Set_Mail_Username'],
                'Type' => 'text',
                'Label' => 'Podaj swój login do konta e-mailowego',
                'Required' => true
            ),
            '5' => array(
                'Name' => 'Set_Mail_Password',
                'Value' => '',
                'Type' => 'password',
                'Label' => 'Podaj swoje hasło do konta e-mailowego',
                'Required' => false
            ),
            
            '6' => array(
                'Name' => 'Avatar_Width',
                'Value' => !array_key_exists('Avatar_Width', $values) ? '100' : $values['Avatar_Width'],
                'Type' => 'text',
                'Label' => 'Podaj maksymalną szerokość avatara (w px)',
                'Required' => true
            ),
            
            '7' => array(
                'Name' => 'Avatar_Height',
                'Value' => !array_key_exists('Avatar_Height', $values)  ? '100' : $values['Avatar_Height'],
                'Type' => 'text',
                'Label' => 'Podaj maksymalną wysokość avatara (w px)',
                'Required' => true
            ),
        );

        return $this->_config = $config;
    }
    
    public function returnOptions()
    {
        $this->_prepareOptions();
        return $this->_config;
    }
    
    public function save($data)
    {
        foreach($data as $key => $value)
        {
            $to_db = array(
                'Name' => $key,
                'Value' => $value
            );
            if($to_db['Value'] == null)
            {

                if(array_key_exists($key, $this->_temp))
                {
                    if(strstr($to_db['Name'], 'Password'))
                    {
                        $to_db['Value'] = $this->encryptPassword($this->_temp[$key]);
                    }
                    else
                    {
                    $to_db['Value'] = $this->_temp[$key];
                    }
                }
            }
            else
            {
                if(strstr($to_db['Name'], 'Password'))
                {
                    $to_db['Value'] = $this->encryptPassword($to_db['Value']);
                }
            }
            

            if(!$this->existOption($to_db['Name']))
            {
                $config = new Config();
                $config ->setName($to_db['Name'])
                    ->setValue($to_db['Value']);
                
                $this->_em->persist($config);
            }
            else
            {

                $config = $this->_em->getRepository('AwojtysTicketServBundle:Config')->findOneBy(array('name' => $to_db['Name']));
                $config->setName($to_db['Name']);
                $config->setValue($to_db['Value']);
            }
            $this->_em->flush();
            $connection = $this->_em->getConnection();
            $query = $connection->prepare('UPDATE User SET switched = 0');
            $query->execute();

        }
    }
}

?>
