<?php
namespace Awojtys\TicketServBundle\DependencyInjection;

use Awojtys\TicketServBundle\Entity\Ticket;


class TicketHelper
{
    protected $_em;
    protected $_config;
    protected $_mailer;
    
    public function __construct(\Doctrine\ORM\EntityManager $em, $mailer)
    {
        $this->_em = $em;
        $this->_mailer = $mailer;
    }
    
    public function save(Ticket $ticket) {
        if(!$ticket->getId())
        {
            $this->_em->persist($ticket);
            $subject = 'Przypisano Tobie nowy ticket';
        }
        else
        {
            $subject = 'Przypisano Ci zedytowany ticket';
        }
        $mail = $this->_em->getRepository('AwojtysTicketServBundle:User')->find($ticket->getAssignee());
        $to = $mail->getEmail();
        
        $body = 'Przypisano Ci następujący ticket: <br />
                ID:' . $ticket->getId() . '<br />
                Status:' . $ticket->getStatus() . '<br />
                Priorytet:' . $ticket->getPriority() . '<br />
                Tytuł:' . $ticket->getTitle() . '<br />
                Treść:' . $ticket->getContent();
        
        $this->_mailer->prepareMail($subject, $to, $body);
        $this->_mailer->sendMail();
        
        
        $this->_em->flush();     
    }
}

?>
