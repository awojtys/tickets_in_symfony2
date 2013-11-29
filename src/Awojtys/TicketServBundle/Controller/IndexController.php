<?php

namespace Awojtys\TicketServBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Awojtys\TicketServBundle\Entity\Ticket;
use Awojtys\TicketServBundle\Entity\Config;
use Awojtys\TicketServBundle\Forms\ConfigForm;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends Controller
{
    public function indexAction()
    {
        $ajax = $this->get('ajax_helper');
        $tickets = $this->getDoctrine()
                ->getRepository('AwojtysTicketServBundle:Ticket')
                ->findAll();
        
        $data_array = $ajax->getArrayFromTicketData($tickets);

        if($this->getRequest()->isXmlHttpRequest())
        {
            return new \Symfony\Component\HttpFoundation\JsonResponse($data_array);
            
        }

        
        if(!$tickets)
        {
            $this->get('session')->getFlashBag()->add('notice', 'Brak ticketów');
        }

        return $this->render('AwojtysTicketServBundle:Index:index.html.twig');
    }
    
    public function configAction()
    {
        $helper = $this->get('config_helper');
        $form = $this->createForm(new ConfigForm($helper));
        
        if($this->getRequest()->isMethod('POST'))
        {
            $form->handleRequest($this->getRequest());
            $data = $form->getData();
            if($form->isValid())
            {
                $helper->save($data);
                $this->get('session')->getFlashBag()->add('notice', 'Ustawienia zmienione!');
        
                return $this->redirect($this->generateUrl('config'));
            }
        }
        return $this->render('AwojtysTicketServBundle:Config:config.html.twig', array('form' => $form->createView()));
    }
}
