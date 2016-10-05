<?php
namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Product;

class Db extends Controller
{
//    protected $em;
    protected $container;

    public function __construct(EntityManager $entityManager, Container $container)
    {
//        $this->em = $entityManager;
        $this->container = $container;
    }

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

    public function createAction( $title, $description, $content, $imageFile, $imageName)
    {
//        var_dump($imageName); exit;
        $product = new Product();
        $product->setTitle($title);
        $product->setDescription($description);
        $product->setContent($content);
        $product->setImageFile($imageFile);
        $product->setImageName($imageName);

        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->flush();

        return new Response('Saved new product with id '.$product->getId());
    }

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

}
