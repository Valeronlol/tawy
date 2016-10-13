<?php

namespace AppBundle\Uploader\Naming;

use Oneup\UploaderBundle\Uploader\File\FileInterface;
use Oneup\UploaderBundle\Uploader\Naming\NamerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class CatNamer implements NamerInterface
{
    private $tokenStorage;
    private $container;

    public function __construct(TokenStorage $tokenStorage, $container){
        $this->tokenStorage = $tokenStorage;
        $this->container = $container;
    }
    public function name(FileInterface $file)
    {
        $userId = $this->tokenStorage->getToken()->getUser()->getId();
        $dbservice = $this->container->get('DBservice');

        return "image_" . uniqid() . ".jpg";
//        return sprintf('%s/%s.%s',
//            $res,
//            uniqid(),
//            $file->getExtension()
//        );
    }
}