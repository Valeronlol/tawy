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

    public function createAction( $title, $description, $content)
    {
        $product = new Product();
        $product->setTitle($title);
        $product->setContent($content);
        $product->setDescription($description);

        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->flush();

        return new Response('Saved new product with id '.$product->getId());
    }

    public function updateAction($productId)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('AppBundle:Product')->find($productId);

        if (!$product) {
            throw $this->createNotFoundException(
                'No product found for id '.$productId
            );
        }

        $product->setContent('Mouse!');
        $product->setDescription('Krasava!');
        $em->flush();

        return $this->redirectToRoute('main_index');
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
