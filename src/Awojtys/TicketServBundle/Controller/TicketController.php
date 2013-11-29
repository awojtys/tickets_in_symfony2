<?php

namespace Awojtys\TicketServBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Awojtys\TicketServBundle\Entity\Ticket;
use Awojtys\TicketServBundle\Forms\TicketForm;

class TicketController extends Controller
{

    
    public function addAction()
    {
        $ticket = new Ticket();
        $form = $this->createForm(new TicketForm(), $ticket);
        
        if($this->getRequest()->isMethod('POST'))
        {
            $form->handleRequest($this->getRequest());
            if($form->isValid())
            {
                $ticket->setAuthor($this->getUser());
                $ticket->setDate(new \DateTime());

                $helper = $this->get('ticket_helper');
                $helper->save($ticket);
                
                $this->get('session')->getFlashBag()->add('notice', 'Ticket został utworzony!');   
                return $this->redirect($this->generateUrl('home'));
            }
        }
        
        return $this->render('AwojtysTicketServBundle:Ticket:add.html.twig', array(
            'form' => $form->createView()
        ));
    }
    
    public function showAction($id)
    {
        $ticket = $this->getDoctrine()
                ->getRepository('AwojtysTicketServBundle:Ticket')
                ->find($id);
        
        if(!$ticket)
        {
            throw  $this->createNotFoundException('Nie znaleziono żadnych elementów');
        }    
        
        return $this->render('AwojtysTicketServBundle:Ticket:show.html.twig', array('id' => $id, 'data' => $ticket));
    }
    
    public function editAction($id)
    {
        $error = '';
        $security = $this->get('security.context')->getToken()->getUser();
        $ticket = $this->getDoctrine()->getRepository('AwojtysTicketServBundle:Ticket')->find($id);
        $temp_date = $ticket->getDate();
        $temp_author = $ticket->getAuthor();
        if($security != 'anon.' && ($ticket->getAuthor()->getId() == $security->getId() || $security->getRole() == 'admin'))
        {
            $form = $this->createForm(new TicketForm(), $ticket);

            if($this->getRequest()->isMethod('POST'))
            {
                $form->handleRequest($this->getRequest());
                if($form->isValid())
                {
                    $helper = $this->get('ticket_helper');
                    $helper->save($ticket);
                    
                    $this->get('session')->getFlashBag()->add('notice', 'Ticket o ID: ' . $id . ' został zmodyfikowany!');
                    return $this->redirect($this->generateUrl('home'));
                }
            }
        }
        else
        {
            $this->get('session')->getFlashBag()->add('notice', 'Nie masz dostępu do tej częsci witryny');
            return $this->redirect($this->generateUrl('home'));
        }
        
        return $this->render('AwojtysTicketServBundle:Ticket:edit.html.twig', array('form' => $form->createView(), 'errors' => $error, 'id' => $id));
    }
    
    public function deleteAction($id)
    {
        $security = $this->get('security.context')->getToken()->getUser();
        $ticket = $this->getDoctrine()->getRepository('AwojtysTicketServBundle:Ticket')->find($id);
        if($security != 'anon.' && ($ticket->getAuthor()->getId() == $security->getId() || $security->getRole() == 'admin'))
        {
            $manager = $this->getDoctrine()->getManager();
            $manager->remove($ticket);
            $manager->flush();

            $this->get('session')->getFlashBag()->add('notice', 'Ticket o ID: ' . $id . ' został skasowany!');
            return $this->redirect($this->generateUrl('home'));
        }
        else
        {
            $this->get('session')->getFlashBag()->add('notice', 'Nie masz dostępu do tej częsci witryny');
            return $this->redirect($this->generateUrl('home'));
        }
    }
}
