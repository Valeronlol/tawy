<?php
namespace Sonata\MediaBundle\Provider;

use Sonata\MediaBundle\Provider\BaseProvider;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\MediaBundle\Model\MediaInterface;
use Symfony\Component\Form\Form;

class VimeoProvider extends BaseProvider
{
    public function buildCreateForm(FormMapper $formMapper)
    {
        $formMapper->add('binaryContent', array(), array('type' => 'string'));
    }

    public function buildEditForm(FormMapper $formMapper)
    {
        $formMapper->add('name');
        $formMapper->add('enabled');
        $formMapper->add('authorName');
        $formMapper->add('cdnIsFlushable');
        $formMapper->add('description');
        $formMapper->add('copyright');
        $formMapper->add('binaryContent', array(), array('type' => 'string'));
    }

    public function getMetadata(MediaInterface $media)
    {
        if (!$media->getBinaryContent()) {

            return;
        }

        $url = sprintf('http://vimeo.com/api/oembed.json?url=http://vimeo.com/%s', $media->getBinaryContent());
        $metadata = @file_get_contents($url);

        if (!$metadata) {
            throw new \RuntimeException('Unable to retrieve vimeo video information for :' . $url);
        }

        $metadata = json_decode($metadata, true);

        if (!$metadata) {
            throw new \RuntimeException('Unable to decode vimeo video information for :' . $url);
        }

        return $metadata;
    }

    public function prePersist(MediaInterface $media)
    {
        if (!$media->getBinaryContent()) {

            return;
        }

        // retrieve metadata
        $metadata = $this->getMetadata($media);

        // store provider information
        $media->setProviderName($this->name);
        $media->setProviderReference($media->getBinaryContent());
        $media->setProviderMetadata($metadata);

        // update Media common field from metadata
        $media->setName($metadata['title']);
        $media->setDescription($metadata['description']);
        $media->setAuthorName($metadata['author_name']);
        $media->setHeight($metadata['height']);
        $media->setWidth($metadata['width']);
        $media->setLength($metadata['duration']);
        $media->setContentType('video/x-flv');
        $media->setProviderStatus(Media::STATUS_OK);

        $media->setCreatedAt(new \Datetime());
        $media->setUpdatedAt(new \Datetime());
    }

    public function preUpdate(MediaInterface $media)
    {
        if (!$media->getBinaryContent()) {

            return;
        }

        $metadata = $this->getMetadata($media);

        $media->setProviderReference($media->getBinaryContent());
        $media->setProviderMetadata($metadata);
        $media->setHeight($metadata['height']);
        $media->setWidth($metadata['width']);
        $media->setProviderStatus(Media::STATUS_OK);

        $media->setUpdatedAt(new \Datetime());
    }

    public function postUpdate(MediaInterface $media)
    {
        $this->postPersist($media);
    }

    public function postPersist(MediaInterface $media)
    {
        if (!$media->getBinaryContent()) {

            return;
        }

        $this->generateThumbnails($media);
    }

    public function getReferenceImage(MediaInterface $media)
    {
        return $media->getMetadataValue('thumbnail_url');
    }

    public function getReferenceUrl(MediaInterface $media)
    {
        return sprintf('http://foobar.com/%s', $media->getProviderReference());
    }
}