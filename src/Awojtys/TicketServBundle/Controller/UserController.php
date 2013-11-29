<?php

namespace Awojtys\TicketServBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Awojtys\TicketServBundle\Forms\EditregForm;
use Awojtys\TicketServBundle\Forms\LoginForm;
use Awojtys\TicketServBundle\Entity\User;
use Symfony\Component\Security\Core\SecurityContext;

class UserController extends Controller
{    
    public function listAction()
    {
        $users = $this->getDoctrine()
                ->getRepository('AwojtysTicketServBundle:User')
                ->findAll();
        
        if(!$users)
        {
            $this->get('session')->getFlashBag()->add('notice', 'Nie znaleziono użytkowników!');
        }
        return $this->render('AwojtysTicketServBundle:User:list.html.twig', array ('users' => $users));
    }
    
    public function loginAction()
    {
        $security = $this->get('security.context')->getToken()->getUser();
        if($security == 'anon.')
        {
            $request = $this->getRequest();
            $session = $request->getSession();
            $form = $this->createForm(new LoginForm());

            if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {

                $error = $request->attributes->get(
                    SecurityContext::AUTHENTICATION_ERROR
                );
            } else {
                $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
                $session->remove(SecurityContext::AUTHENTICATION_ERROR);
            }
            return $this->render(
                'AwojtysTicketServBundle:User:login.html.twig',
                array(
                    // last username entered by the user
                    'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                    'error'         => $error,
                    'form' => $form->createView()
                )
            );
        }
        else
        {
            return $this->redirect($this->generateUrl('home'));
        }
    }
    
    public function registerAction()
    {       
        $error = '';
        $user = new User();
        $form = $this->createForm(new EditregForm(), $user);

        if($this->getRequest()->isMethod('POST'))
        {
            $form->handleRequest($this->getRequest());
            if($form->isValid())
            {
                $helper = $this->get('users_helper');
                if($helper->userExist($user->getNickname()) == false)
                {
                    $user->setAvatar('none.png');
                    $user->setPassword($helper->hashPassword($user->getPassword()));
                    $user->setSwitched(1);
                    $helper->save($user);
                        
                    $this->get('session')->getFlashBag()->add('notice', 'Użytkownik został utworzony!');
        
                    return $this->redirect($this->generateUrl('user_list'));
                }
                else
                {
                    $error = 'Użytkownik już istnieje';
                }
            }
            else
            {
                $error = 'Formularz jest nieprawidłowy';
            }
        }
        
        return $this->render('AwojtysTicketServBundle:User:register.html.twig', array ('form' => $form->createView(), 'errors' => $error));
    }
       
    public function editAction($id)
    {
        $security = $this->get('security.context')->getToken()->getUser();
        if($this->getRequest()->get('id') == $security->getId() || $security->getRole() == 'admin')
        {
            $error = '';
            $user = $this->getDoctrine()->getRepository('AwojtysTicketServBundle:User')->find($id);

            $user_temp = $user ->getNickname();
            $password_temp = $user->getPassword();
            $avatar_temp = $user->getAvatar();
            $role_temp = $user->getRole();
            $validate['message'] = '';
            
            $form = $this->createForm(new EditregForm(false), $user);
            if($this->getRequest()->isMethod('POST'))
            {
                try
                {
                    $form -> bind($this->getRequest());
                }
                catch(\Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException $e)
                {
                    echo $e;
                    die();
                }
                if($form->isValid())
                {
                    $helper = $this->get('users_helper');
                    $images = $this->get('image_operations');

                    if($helper->userExist($user->getNickname()) == false || $user_temp == $user->getNickname())
                    {
                        //upload obrazka
                        $uploaded_image = $form->get('Avatar')->getData();
                        if($uploaded_image != null)
                        {
                            $validate = $images -> validateImage($uploaded_image, $id);
                            $images -> imageResize($validate['filename']);
                            $user->setAvatar($validate['filename']);  
                        }
                        elseif($avatar_temp == 'none.png')
                        {
                            $user->setAvatar('none.png');  
                        }
                        else
                        {
                            $user->setAvatar($avatar_temp);
                        }
                        
                        if($user->getRemove() == true)
                        {
                            $images->removeImages($id);
                        }
                        
                        //koniec uploadu avatara
                        
                        if($user->getPassword() != '')
                        {
                            $user->setPassword($helper->hashPassword($user->getPassword()));
                        }
                        else
                        {
                            $user->setPassword($password_temp);
                        }
                        if($user->getRole() == '')
                        {
                            $user->setRole($role_temp);
                        }
                        
                        $user->setSwitched(1);
                        $helper->save($user);

                        $this->get('session')->getFlashBag()->add('notice', 'Użytkownik został zmodyfikowany!');
                        $this->get('session')->getFlashBag()->add('notice', $validate['message']);

                        return $this->redirect($this->generateUrl('user_profil', array('id' => $security->getId())));
                    }
                    else
                    {
                        $error = 'Użytkownik już istnieje';
                    }
                }
                else
                {
                    $error = 'Formularz jest nieprawidłowy';
                }
            }
            if($security->getRole() != 'admin' || $user->getRole() == 'admin')
            {
                $form->remove('Role');
            }
            return $this->render('AwojtysTicketServBundle:User:edit.html.twig', array(
                'error' => $error,
                'user' => $user,
                'id' => $id,
                'form' => $form->createView()
            ));
        }
        else
        {
            $this->get('session')->getFlashBag()->add('notice', 'Nie masz dostępu do tej częsci witryny');
            return $this->redirect($this->generateUrl('user_profil', array('id' => $security->getId())));
        }
    }
       
    public function showAction($id)
    {
        $security = $this->get('security.context')->getToken()->getUser();
        $image = $this->get('image_operations');
        
        if($this->getRequest()->get('id') == $security->getId() || $security->getRole() == 'admin')
        {
            $user = $this->getDoctrine()
                    ->getRepository('AwojtysTicketServBundle:User')
                    ->find($id);
            
            if($user->getSwitched() == 0)
            {
                $image->imageResize($user->getAvatar());
            }
            
            $filename = $image->getRename($user->getAvatar(), $id);
            
            $tickets_by_author = $this->getDoctrine()
                    ->getRepository('AwojtysTicketServBundle:Ticket')
                    ->findBy(array('author' => $id));

            $tickets_by_assignee = $this->getDoctrine()
                    ->getRepository('AwojtysTicketServBundle:Ticket')
                    ->findBy(array('assignee' => $id));

            return $this->render('AwojtysTicketServBundle:User:show.html.twig', 
                    array('user_data' => $user, 
                          'tickets_by_author' => $tickets_by_author, 
                          'tickets_by_assignee' => $tickets_by_assignee,
                          'avatar' => $filename
                    ));
        }
        else
        {
            $this->get('session')->getFlashBag()->add('notice', 'Nie masz dostępu do tej częsci witryny');
            return $this->redirect($this->generateUrl('user_profil', array('id' => $security->getId())));
        }
    }
    
    public function deleteAction($id)
    {
        $security = $this->get('security.context')->getToken()->getUser();
        $manager = $this->getDoctrine()->getManager();
        $user = $manager->getRepository('AwojtysTicketServBundle:User')->find($id);
        
        if($id == $security->getId() || ($security->getRole() == 'admin' && $user->getRole() != 'admin'))
        {
            try
            {
                $manager ->remove($user);
                $manager ->flush();
                $this->get('session')->getFlashBag()->add('notice', 'Użytkownik o ID: ' . $id . ' został skasowany!');
                return $this->redirect($this->generateUrl('user_list'));
            }
            catch(\Exception $e)
            {
                $this->get('session')->getFlashBag()->add('notice', 'Nie udało się skasować użytkownika. sprawdź czy wszystkie jego tickety oraz przypisane tickety do niego zostały skasowane');
                return $this->redirect($this->generateUrl('user_list'));
            }
        }
        else
        {
            $this->get('session')->getFlashBag()->add('notice', 'Nie masz dostępu do tej częsci witryny');
            return $this->redirect($this->generateUrl('user_profil', array('id' => $security->getId())));
        }
    }
}
