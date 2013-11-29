<?php

namespace Awojtys\TicketServBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface, \Serializable
{
    protected $id;

    protected $nickname;

    protected $password;

    protected $email;

    protected $role;

    protected $avatar;

    protected $switched;

    protected $_remove;
    
     protected $tickets;
     protected $tickets2;

     public function __construct()
     {
         $this->tickets = new ArrayCollection();
         $this->tickets2 = new ArrayCollection();
         
     }
     
     public function setRemove($remove)
     {
         $this->_remove = $remove;
         return $this;
     }
     
     public function getRemove()
     {
         return $this->_remove;
     }
     
     public function setId($id)
     {
         $this->id = $id;
         return $this;
     }
     
    public function getId()
    {
        return $this->id;
    }

    public function setNickname($nickname)
    {
        $this->nickname = $nickname;
    
        return $this;
    }

    public function getNickname()
    {
        return $this->nickname;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    
        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setRole($role)
    {
        $this->role = $role;
    
        return $this;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
    
        return $this;
    }

    public function getAvatar()
    {
        return $this->avatar;
    }

    public function setSwitched($switched)
    {
        $this->switched = $switched;
    
        return $this;
    }
    
    public function getSwitched()
    {
        return $this->switched;
    }

    public function addTicket(\Awojtys\TicketServBundle\Entity\Ticket $tickets)
    {
        $this->tickets[] = $tickets;
    
        return $this;
    }

    public function removeTicket(\Awojtys\TicketServBundle\Entity\Ticket $tickets)
    {
        $this->tickets->removeElement($tickets);
    }

    public function getTickets()
    {
        return $this->tickets;
    }
    

    /**
     * @var \Doctrine\Common\Collections\Collection
     */


    /**
     * Add tickets2
     *
     * @param \Awojtys\TicketServBundle\Entity\Ticket $tickets2
     * @return User
     */
    public function addTickets2(\Awojtys\TicketServBundle\Entity\Ticket $tickets2)
    {
        $this->tickets2[] = $tickets2;
    
        return $this;
    }

    /**
     * Remove tickets2
     *
     * @param \Awojtys\TicketServBundle\Entity\Ticket $tickets2
     */
    public function removeTickets2(\Awojtys\TicketServBundle\Entity\Ticket $tickets2)
    {
        $this->tickets2->removeElement($tickets2);
    }

    /**
     * Get tickets2
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTickets2()
    {
        return $this->tickets2;
    }
    
    public function __toString() {
        return $this->getNickname();
    }
    
    public function getRoles() {
        if($this->role == 'admin')
        {
            return array('ROLE_ADMIN');
        }
        elseif($this->role == 'user')
        {
            return array('ROLE_USER');
        }
    }
    
    public function getSalt() {
        
    }
    
    public function getUsername() {
        return $this->nickname;
    }
    
    public function eraseCredentials() {
    }
    
    public function serialize()
    {
        return serialize(array(
            $this->id,
        ));
    }
    
    public function unserialize($serialized) {
        list (
            $this->id,
        ) = unserialize($serialized);
    }
}