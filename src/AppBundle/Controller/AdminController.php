<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Form;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AdminController extends MainController
{
    //Nesting service array
    public function getData(){
        return array_merge( parent::getData(), array(
            'test' => 'asd',
            'test2' => '31213',
            'admin_buttons' => array(
                array(
                    'title' => 'Добавить статью',
                    'i'     => 'fa fa-file-code-o active fa-lg',
                    'id'    => 'add_art',
                    'slug'  => 'add'
                ),
                array(
                    'title' => 'Редактировать статью',
                    'i'     => 'fa fa-wrench fa-lg',
                    'id'    => 'edit_art',
                    'slug'  => 'edit'
                ),
                 array(
                    'title' => 'Удалить статью',
                    'i'     => 'fa fa-times fa-lg',
                    'id'    => 'remove_art',
                    'slug'  => 'remove'
                )
            ),

        ));
    }

    public function indexAction(Request $request)
    {
//        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!'); //FULL ACCESS DENY

        //Buttons redirect
        $slug = isset($_GET['slug']) ? $_GET['slug'] : null;
        switch ($slug) {
            case 'edit':
                return $this->render( "admin/edit.html.twig", $this->getData());
            case 'add':

                // Create form
                $form = new Form();
                $form = $this->createFormBuilder($form)
                    ->add('title', TextType::class)
                    ->add('description', TextType::class)
                    ->add('content', 'textarea', array(
                        'attr' => array('cols' => '120', 'rows' => '30')))
                    ->add('save', SubmitType::class, array('label' => 'Отправить'))
                    ->getForm();
                $this->setData( array('form' => $form->createView()) );
                $form->handleRequest($request);

                //Form validation
                if ($form->isSubmitted() && $form->isValid()) {
                    $validFormData = $form->getData(); //obj
                    $response = $this->forward('AppBundle:Db:create', array(
                        'title'  => $validFormData->title,
                        'description' => $validFormData->description,
                        'content' => $validFormData->content,
                    ));
                    if ($response)
                        return $this->redirect($this->generateUrl('admin_index'));
                }

                return $this->render( "admin/add.html.twig", $this->getData() );

            case 'remove':
                return $this->render( "admin/remove.html.twig", $this->getData());
            default:
                return $this->render( "admin/admin.html.twig", $this->getData());
        }
    }

    public function editAction(){

        return $this->render( "admin/edit.html.twig", $this->getData());
    }


}
