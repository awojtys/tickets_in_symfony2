<?php

namespace Awojtys\TicketServBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


class Ticket
{
    
    protected $id;
     
    protected $author;
     
    protected $title;

    protected $content;

    protected $status;
    
    protected $assignee;
    
    protected $date;
 
    protected $priority;
     
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
     * Set author
     *
     * @param string $author
     * @return Ticket
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    
        return $this;
    }

    /**
     * Get author
     *
     * @return string 
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Ticket
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Ticket
     */
    public function setContent($content)
    {
        $this->content = $content;
    
        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set assignee
     *
     * @param integer $assignee
     * @return Ticket
     */
    public function setAssignee($assignee)
    {
        $this->assignee = $assignee;
    
        return $this;
    }

    /**
     * Get assignee
     *
     * @return integer 
     */
    public function getAssignee()
    {
        return $this->assignee;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Ticket
     */
    public function setDate($date)
    {
        $this->date = $date;
    
        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }
    
    /**
     * Set date
     *
     * @param \smallint $status
     * @return Ticket
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get date
     *
     * @return \Ticket
     */
    public function getStatus()
    {
        return $this->status;
    }
    
    /**
     * Set date
     *
     * @param \smallint $priority
     * @return Ticket
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    
        return $this;
    }

    /**
     * Get date
     *
     * @return \Ticket
     */
    public function getPriority()
    {
        return $this->priority;
    }

    
}