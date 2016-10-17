<?php
namespace AppBundle\Services;

use AppBundle\Controller\MainController;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\Container;
//use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Product;

class Db extends MainController
{
    protected $container;

    public function __construct(EntityManager $entityManager, Container $container)
    {
        $this->container = $container;
    }

    /**
     * Find elements
     * @param string $id
     * @return Product|\AppBundle\Entity\Product[]|array|object
     */
    public function findProd( $id = 'all' )
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Product');

        if ($id === 'all'){
            return $repository->findAll();
        }
        else{
            return $repository->find($id);
        }
    }

    /**
     * @param integer $productId
     * @return Response
     */
    public function showAction($productId)
    {
        $product = $this->getDoctrine()
            ->getRepository('AppBundle:Product')
            ->find($productId);

        if (!$product) {
            throw $this->createNotFoundException(
                'No product found for id '.$productId
            );
        }

        return new Response('description: '.$product->getDescription() . '<br>'
            .'price: '. $product->getId()
        );
    }

    /**
     * @param string $title
     * @param string $description
     * @param string $content
     * @param object null $imageFile
     * @param string null $imageName
     * @return Response
     */
    public function createAction( $title, $description, $content, $imageFile = null, $imageName = null)
    {
        $product = new Product();
        $product->setTitle($title);
        $product->setDescription($description);
        $product->setContent($content);
        $product->setImageFile($imageFile);
        $product->setImageName($imageName);

        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->flush();

        return new Response($product->getId());
    }

    /**
     * @param array $ajaxData
     * @return bool
     */
    public function updateAction($ajaxData)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('AppBundle:Product')->find($ajaxData['art_id']);

        if (!$product) {
            throw $this->createNotFoundException(
                'No data found for id ' . $ajaxData
            );
        }

        $product->setContent($ajaxData['art_content']);
        $product->setTitle($ajaxData['art_title']);
        $product->setDescription($ajaxData['art_description']);
        $em->flush();

        return true;
    }

    /**
     * Deleting by id
     * @param integer $productId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeAction($productId)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('AppBundle:Product')->find($productId);

        if (!$product) {
            throw $this->createNotFoundException(
                'No product found for id '.$productId
            );
        }

        $em->remove($product);
        $em->flush();

        return $this->redirectToRoute('main_index');
    }

    /**
     * Ajax only function
     * @param integer $productId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAjaxImagesAction($productId, $url)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('AppBundle:Product')->find($productId);
        $imgarr = $product->getImages($url);

        if ( $imgarr )
        {
            $imgarr = unserialize($imgarr);
            $imgarr[] = $url;
            $product->setImages(serialize($imgarr));
        }
        else
        {
            $product->setImages(serialize(array($url)));
        }
        $em->flush();

        return $this->redirectToRoute('main_index');
    }
}
