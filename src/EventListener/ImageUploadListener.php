<?php

namespace App\EventListener;

use App\Service\ImageOptimizer;
use Psr\Log\LoggerInterface;
use Vich\UploaderBundle\Event\Event;

class ImageUploadListener
{
    private $imageOptimizer;
    private $logger;

    public function __construct(ImageOptimizer $imageOptimizer, LoggerInterface $logger)
    {
        $this->imageOptimizer = $imageOptimizer;
        $this->logger = $logger;
    }

    public function onVichUploaderPostUpload(Event $event)
    {
        $object = $event->getObject();
        $mapping = $event->getMapping();

        // resize image and make thumbs
        try {

            if (!is_null($object->getImagePortraitFile())) {
                $this->imageOptimizer->resize($object->getImagePortraitFile()->getRealPath());
            }

            if (!is_null($object->getImageEntireFile())) {
                $this->imageOptimizer->resize($object->getImageEntireFile()->getRealPath());
            }

        }
        catch (exception $e) {

            $this->logger->error($e);

        }
    }
}
