<?php
namespace Awojtys\TicketServBundle\DependencyInjection;

use Awojtys\TicketServBundle\Entity\Ticket;


class Mailer
{
    protected $_em;
    protected $_config;
    protected $_mailer;
    
    protected $_returned_config;
    protected $_returned_mail;
    public function __construct(\Doctrine\ORM\EntityManager $em, \Awojtys\TicketServBundle\DependencyInjection\ConfigHelper $config, $mailer)
    {
        $this->_em = $em;
        $this->_config = $config;
        $this->_mailer = $mailer;
    }
    
    protected function _prepareConfig()
    {
        $options = $this->_config ->getAllOptions();
        
        $transport = \Swift_SmtpTransport::newInstance()
                ->setUsername($options['Set_Mail_Username'])
                ->setPassword($options['Set_Mail_Password'])
                ->setHost($options['Host_Name'])
                ->setPort($options['Set_Port'])
                ->setEncryption($options['Set_Encryption']);
        
        $this->_mailer = \Swift_Mailer::newInstance($transport);
    }
    
    public function prepareMail($subject, $to, $body)
    {
        $message = \Swift_Message::newInstance()
                ->setSubject($subject)
                ->setFrom('andrzej.wojtys@polcode.net')
                ->setTo($to)
                ->setBody($body)
                ->setContentType('text/html');
        return $this->_returned_mail = $message;        
   }
   
   public function SendMail()
   {
       $this->_prepareConfig();
       $this->_mailer->send($this->_returned_mail);
   }
}

?>
