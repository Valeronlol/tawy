<?php
namespace AppBundle\Controller;

use AppBundle\Form\adminAdd;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
     * Add article page, /admin/add
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $request)
    {
        $dbservice = $this->get('DBservice');

        $form = $this->createForm(adminAdd::class, new Product);
        $form->handleRequest($request);
        $this->setData( array('form' => $form->createView()) );

        //Form validation
        if ( $form->isSubmitted() && $form->isValid() )
        {
            $validFormData = $form->getData(); //obj
            $res = $dbservice->createAction(
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

            /**
             * Create session post id
             */
            $id = intval($res->getContent());
            $session = $this->getRequest()->getSession();
            $session->set('id', $id);

            return $this->render( "admin/addimg.html.twig", $this->getData());
        }
        return $this->render( "admin/add.html.twig", $this->getData() );
    }

    /**
     * Ajax only function
     * @param integer $productId
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

        $data = array_merge( $data, array(
            'message' => 'Статья отредактирована',
            'default_message' => 'Панель администратора'
        ) );
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
        $data = array(
            'message' => 'Статья удалена',
            'default_message' => 'Панель администратора'
        );
        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxRemoveImgAction(Request $request)
    {
        $data = $request->request->all();
        $id = $this->container->get('session')->get('id');
        $em = $this->getDoctrine()->getManager();

        $arcticle = $em->getRepository('AppBundle:Product')->find($id);
        $imgarr = unserialize($arcticle->getImages(  ));
        $key = array_search( $data['url'] , $imgarr );
        unset($imgarr[$key]);

        $arcticle->setImages(serialize($imgarr));
        $em->flush();

        return new JsonResponse($imgarr);
    }

}
