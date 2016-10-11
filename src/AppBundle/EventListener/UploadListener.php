<?php

namespace AppBundle\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use Oneup\UploaderBundle\Event\PostPersistEvent;
use Symfony\Component\HttpFoundation\File\File;

class UploadListener
{
    /**
     * @var ObjectManager
     */
    private $om;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    public function onUpload(PostPersistEvent $event)
    {
        $request = $event->getRequest();
        $request->get('img');
    }
}