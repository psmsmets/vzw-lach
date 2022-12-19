<?php

namespace App\EventListener;

use App\Service\ImageOptimizer;
use Vich\UploaderBundle\Event\Event;

class ImageUploadListener
{
    public function __construct(ImageOptimizer $imageOptimizer)
    {
        $this->imageOptimizer = $imageOptimizer;

        // Accessing the session in the constructor is *NOT* recommended, since
        // it might not be accessible yet or lead to unwanted side-effects
        // $this->session = $requestStack->getSession();
    }

    public function onVichUploaderPostUpload(Event $event)
    {
        $object = $event->getObject();
        $mapping = $event->getMapping();

        // resize image and make thumbs 
        $this->imageOptimizer->resize($object->getImagePortraitFile()->getRealPath());
    }
}
