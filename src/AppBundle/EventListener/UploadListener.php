<?php
namespace AppBundle\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use Oneup\UploaderBundle\Event\PostPersistEvent;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;

class UploadListener
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @var @service_container
     */
    private $container;

    /**
     * @var RequestStack
     */
    private $request;

    /**
     * UploadListener constructor.
     * @param ObjectManager $om
     * @param $container
     * @param RequestStack $requestStack
     */
    public function __construct(ObjectManager $om, $container, RequestStack $requestStack)
    {
        $this->container = $container;
        $this->om = $om;
        $this->request = $requestStack;
    }

    /**
     * @param PostPersistEvent $event
     */
    public function onUpload(PostPersistEvent $event)
    {
        $dbservice = $this->container->get('DBservice');
        $file = $event->getFile();
        $fileUrl = $file->getPathName();
        $productId = $this->container->get('session')->get('id');
        $dbservice->addAjaxImagesAction($productId, $fileUrl);

        die ( json_encode($fileUrl) );
    }

}