<?php

namespace Awojtys\TicketServBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;


class Priority
{
    
    protected $id;
    
    protected $name;

    protected $tickets;

     public function __construct()
     {
         $this->tickets = new ArrayCollection();
     }

    public function getId()
    {
        return $this->id;
    }

    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    public function getName()
    {
        return $this->name;
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
      public function __toString() {
        
        return $this->getName();
    }
}