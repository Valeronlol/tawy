<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Form;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use AppBundle\Entity\Product;

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
        if( $slug == 'add'){
            $form = new Product();
            $form = $this->createFormBuilder($form)
                ->add('title', TextType::class)
                ->add('description', TextType::class)
                ->add('content', 'textarea', array(
                    'attr' => array('cols' => '120', 'rows' => '30')))
                ->add('imageFile', FileType::class, array('required' => false, 'label' => 'Картинка'))
                ->add('save', SubmitType::class, array('label' => 'Отправить'))
                ->getForm();
            $form->handleRequest($request);
            $this->setData( array('form' => $form->createView()) );

            //Form validation OK
            if ($form->isSubmitted() && $form->isValid()) {
                $validFormData = $form->getData(); //obj

                $dbservice->createAction(
                    $validFormData->getTitle(),
                    $validFormData->getDescription(),
                    $validFormData->getContent(),
                    $validFormData->getImageFile(),
                    $validFormData->getImageName()
                );

                $allprod = $dbservice->findProd();
                $this->setData(array('all' => $allprod));
                $this->setData(array('chetko' => 'Статья добавлена!'));
                return $this->redirectToRoute('admin_index');
            }
            return $this->render( "admin/add.html.twig", $this->getData() );
        }
        else{
            $allprod = $dbservice->findProd();
            $this->setData(array('all' => $allprod));
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
