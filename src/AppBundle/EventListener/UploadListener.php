<?php
namespace AppBundle\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use Oneup\UploaderBundle\Event\PostPersistEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class UploadListener
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @var service_container
     */
    private $container;
    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(ObjectManager $om, $container, RequestStack $requestStack)
    {
        $this->container = $container;
        $this->om = $om;
        $this->requestStack = $requestStack;
    }

    public function onUpload(PostPersistEvent $event)
    {
        $dbservice = $this->container->get('DBservice');
        $file = $event->getFile();
        $fileUrl = $file->getPathName();
//        $fileName = $file->getFileName();
        $productId = $this->container->get('session')->get('id');

        $dbservice->addAjaxImagesAction($productId, $fileUrl);
//        var_dump($fileUrl); exit;
    }

}