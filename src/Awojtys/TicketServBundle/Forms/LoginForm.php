<?php

namespace Awojtys\TicketServBundle\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class LoginForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $values = null)
    {
        $builder ->add('_username', 'text', array('label' => 'Nazwa użytkownika'));
        $builder ->add('_password', 'password', array('label' => 'Hasło'));
        $builder ->add('Submit', 'submit');
        $builder ->setAction('login_check');
        $builder ->setMethod('post');
    }
  
    public function getName()
    {
        return null;
        
        
    }
}
?>
