<?php

namespace Awojtys\TicketServBundle\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\File;

class EditregForm extends AbstractType {
    protected $_required;
    
    public function __construct($required = true) {
        return $this->_required = $required;
    }

    public function buildForm(FormBuilderInterface $builder, array $values = null)
    {
        $builder->add('Nickname', null, array(
            'label' => 'Nazwa użytkownika',
            'constraints' => array(
                new NotBlank(),
                new Length(array('min' => 5, 'max' => 255)),
            )))
                ->add('Password', 'repeated', array(
                    'type' => 'password',
                    'invalid_message' => 'The password fields must match',
                    'required' => true,
                    'first_options' => array('label' => 'Hasło'),
                    'second_options' => array('label' => 'Powtórz hasło'),
                    'constraints' => array(
                        new Length(array('min' => 8, 'max' => 255))
                    )))
                ->add('Email', 'repeated', array(
                    'type' => 'text',
                    'invalid_message' => 'The password fields must match',
                    'required' => true,
                    'first_options' => array('label' => 'Email'),
                    'second_options' => array('label' => 'Powtórz email'),
                    'constraints' => array(
                        new Length(array('max' => 255)),
                        new Email()
                    )))
                ->add('Role', 'choice', array(
                    'choices' => array('user' => 'Użytkownik', 'admin' => 'Administrator')
                ));
                
                if($this->_required == false)
                {
                    $builder->add('Avatar', 'file', array(
                        'data_class' => null,
                        'label' => 'Dodaj Avatar'
                        ));
                    $builder->add('Remove', 'checkbox', array('label' => 'Usuń Avatar'));
                }
                
        $builder->add('submit', 'submit');
    }
    
    public function getName()
    {
        return 'editreg';
    }
    
    public function setDefaultOptions(\Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver)
    {
        if($this->_required == true)
        {
            $resolver->setDefaults(array(
                'required' => true,
            ));
        }
        else
        {
            $resolver->setDefaults(array(
                'required' => false,
            ));    
        }
        
        
    }
}

?>
