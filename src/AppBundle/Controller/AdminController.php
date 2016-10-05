<?php
namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use AppBundle\Entity\Product;

class AdminController extends MainController
{
    /**
     * Admin page
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $dbservice = $this->get('DBservice');
        $allprod = $dbservice->findProd();
        $this->setData(array(
            'all' => $allprod,
            'chetko' => 'Панель администратора'
        ));
        return $this->render( "admin/admin.html.twig", $this->getData());
    }

    /**
     * Add article page, admin/add
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $request)
    {
        $dbservice = $this->get('DBservice');
        $form = $this->createFormBuilder( new Product )
            ->add('title', TextType::class)
            ->add('description', TextType::class)
            ->add('content', 'textarea', array( 'attr' => array('cols' => '120', 'rows' => '30' )))
            ->add('imageFile', FileType::class, array('required' => false, 'label' => 'Миниатюра'))
            ->add('save', SubmitType::class, array('label' => 'Отправить'))
            ->getForm();

        $form->handleRequest($request);
        $this->setData( array('form' => $form->createView()) );

        //Form validation
        if ($form->isSubmitted() && $form->isValid())
        {
            $validFormData = $form->getData(); //obj
            $dbservice->createAction(
                $validFormData->getTitle(),
                $validFormData->getDescription(),
                $validFormData->getContent(),
                $validFormData->getImageFile(),
                $validFormData->getImageName()
            );
            $allprod = $dbservice->findProd();

            $this->setData(array(
                'chetko' => 'Статья добавлена!',
                'all' => $allprod
            ));
            return $this->redirectToRoute('admin_index');
        }
        return $this->render( "admin/add.html.twig", $this->getData() );
    }

    /**
     * Ajax only function
     * @param $productId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction($productId)
    {
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

    /**
     * AJAX only function
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function saveEditAction(Request $request)
    {
        $data = $request->request->all();
        $dbservice = $this->get('DBservice');
        $dbservice->updateAction($data);

        return new JsonResponse($data);
    }

    /**
     * AJAX only function
     * @param string $productId
     * @return bool|\Symfony\Component\HttpFoundation\Response
     */
    public function removeAction($productId)
    {
        if (is_numeric($productId))
        {
            $dbservice = $this->get('DBservice');
            $dbservice->removeAction($productId);
        }
        else
            return false;

        return $this->render( "admin/remove.html.twig", $this->getData());
    }

}
