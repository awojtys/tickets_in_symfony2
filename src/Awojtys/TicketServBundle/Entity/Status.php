<?php

namespace Awojtys\TicketServBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;


class Status
{
        protected $tickets;

   
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

 public function __construct()
    {
        $this->tickets = new ArrayCollection();
    }
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Status
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }
  

    /**
     * Add tickets
     *
     * @param \Awojtys\TicketServBundle\Entity\Ticket $tickets
     * @return Status
     */
    public function addTicket(\Awojtys\TicketServBundle\Entity\Ticket $tickets)
    {
        $this->tickets[] = $tickets;
    
        return $this;
    }

    /**
     * Remove tickets
     *
     * @param \Awojtys\TicketServBundle\Entity\Ticket $tickets
     */
    public function removeTicket(\Awojtys\TicketServBundle\Entity\Ticket $tickets)
    {
        $this->tickets->removeElement($tickets);
    }

    /**
     * Get tickets
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTickets()
    {
        return $this->tickets;
    }
    
    public function __toString() {
        
        return $this->getName();
    }
}