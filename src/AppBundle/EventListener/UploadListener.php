<?php
namespace AppBundle\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use Oneup\UploaderBundle\Event\PostPersistEvent;
use Symfony\Component\HttpFoundation\Session\Session;

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

    public function __construct(ObjectManager $om, $container)
    {
        $this->container = $container;
        $this->om = $om;
    }

    public function onUpload(PostPersistEvent $event)
    {
        $dbservice = $this->container->get('DBservice');
        $file = $event->getFile();
        $fileName = $file->getFileName();
        $fileUrl = $file->getPathName();

//        echo $dbservice->saveImgAction($fileName, $fileUrl);
    }

}