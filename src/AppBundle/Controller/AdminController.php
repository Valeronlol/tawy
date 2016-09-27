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
            'admin_buttons' => array(
                array(
                    'title' => 'Добавить статью',
                    'i'     => 'fa fa-file-code-o active fa-lg',
                    'id'    => 'add_art',
                    'slug'  => 'add'
                ),
            ),

        ));
    }

    public function indexAction(Request $request)
    {
        // Database service
        $dbservice = $this->get('DBservice');

        //Buttons redirect
        $slug = isset($_GET['slug']) ? $_GET['slug'] : null;
        switch ($slug) {
            case 'edit':
                return $this->render( "admin/edit.html.twig", $this->getData());
            case 'add':

                // Create ADD form
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

                //Form validation OK
                if ($form->isSubmitted() && $form->isValid()) {
                    $validFormData = $form->getData(); //obj

                    $dbservice->createAction( $validFormData->title, $validFormData->description, $validFormData->content );
                    if ($dbservice){
                        $allprod = $dbservice->findProd();
                        $this->setData(array('all' => $allprod));
                        $this->setData(array('chetko' => 'Статья добавлена!'));
                        return $this->render('admin/admin.html.twig', $this->getData());
                    }
                }

                return $this->render( "admin/add.html.twig", $this->getData() );

            case 'remove':
                return $this->render( "admin/remove.html.twig", $this->getData());
            default:

                //Реализовать вывод всех статей
                $allprod = $dbservice->findProd();
                $this->setData(array('all' => $allprod));
//                print_r($allprod->getId()); exit;

                return $this->render( "admin/admin.html.twig", $this->getData());
        }
    }

    public function editAction($productId){
        echo $productId; exit;
//        return $this->render( "admin/edit.html.twig", $this->getData());
    }

    public function removeAction($productId){
        // Database service
        $dbservice = $this->get('DBservice');
        $dbservice->removeAction($productId);
        return $this->redirectToRoute('admin_index');
    }

}
