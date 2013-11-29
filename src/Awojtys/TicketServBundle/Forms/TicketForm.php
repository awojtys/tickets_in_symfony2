<?php

namespace Awojtys\TicketServBundle\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Doctrine\ORM\EntityRepository;

class TicketForm extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('Title', 'text', array(            
            'constraints' => array(
                new NotBlank(),
                new Length(array('min' => 3, 'max' => 255)))));
        $builder->add('Content', 'textarea', array(
            'attr' => array(
                'rows' => 10,
                'cols' => 50
            )
        ));
        $builder->add('Status', 'entity', array(
            'constraints' => array(
                new NotBlank()),
            'class' => 'AwojtysTicketServBundle:Status',
            'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('s');
            }
        ));
        $builder->add('Priority', 'entity', array(
            'class' => 'AwojtysTicketServBundle:Priority',
            'query_builder' => function(EntityRepository $er) {
                return $er->createQuerybuilder('p');
            }
        ));
        $builder->add('Assignee', 'entity', array(
            'required' => false,
            'class' => 'AwojtysTicketServBundle:User',
            'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('u');
            }
        ));
        
        $builder->add('Submit', 'submit');
    }
    
    public function getName()
    {
        return 'AddTicket';
    }
}

?>
