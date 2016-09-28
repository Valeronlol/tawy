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
//            case 'edit':
//                return $this->render( "admin/edit.html.twig", $this->getData());
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

//            case 'remove':
//                return $this->render( "admin/remove.html.twig", $this->getData());
            default:

                //Реализовать вывод всех статей
                $allprod = $dbservice->findProd();
                $this->setData(array('all' => $allprod));
//                print_r($allprod->getId()); exit;
                $this->setData(array('chetko' => 'Панель администратора'));
                return $this->render( "admin/admin.html.twig", $this->getData());
        }
    }

    public function editAction($productId){

        $dbservice = $this->get('DBservice');
        $article = $dbservice->findProd($productId);

        $this->setData(array(
                "art_id" => $article->getId(),
                "art_title" => $article->getTitle(),
                "art_content" => $article->getContent(),
                "art_description" => $article->getDescription()
        ));
        return $this->render( "admin/ajax/edit.html.twig", $this->getData());
    }

    public function saveEditAction(Request $request){
        $data = $request->getContent();
        parse_str($data, $output);

        $dbservice = $this->get('DBservice');
        if ($dbservice->updateAction($output))
            print json_encode($output);

        return $this->render( "admin/ajax/save.html.twig");
    }

    public function removeAction($productId){

        if (is_numeric($productId)){
            $dbservice = $this->get('DBservice');
            $dbservice->removeAction($productId);
        }
        else
            return false;
        return $this->render( "admin/remove.html.twig", $this->getData());
    }

}
