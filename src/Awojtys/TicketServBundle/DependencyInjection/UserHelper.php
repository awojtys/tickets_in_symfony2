<?php
namespace Awojtys\TicketServBundle\DependencyInjection;

use Awojtys\TicketServBundle\Entity\User;

class UserHelper
{
    protected $_em;
    
    public function __construct(\Doctrine\ORM\EntityManager $em)
    {
        $this->_em = $em;
    }
    
    public function hashPassword($password)
    {
        if (CRYPT_SHA512 == 1) {
            $password = hash('sha512', $password);
        }
        else
        {
            $password = hash('md5', $password);
        }
        return $password;
    }
    
    public function userExist($nickname)
    {
        $query = $this->_em->getRepository('AwojtysTicketServBundle:User')->findBy(array('nickname' => $nickname));
        if($query)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    public function save(User $user) {
        
        if(!$user->getId())
            $this->_em->persist($user);
        $this->_em->flush();     
    }
}

?>
