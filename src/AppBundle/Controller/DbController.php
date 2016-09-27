<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Product;

class DbController extends Controller
{
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
            .'id: '. $product->getId()
        );
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

        $product->setName('Mouse!');
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
