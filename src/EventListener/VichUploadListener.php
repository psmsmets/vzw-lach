<?php

namespace App\EventListener;

use App\Entity\Associate;
use App\Service\ImageOptimizer;
use Vich\UploaderBundle\Event\Event;

class VichUploadListener
{
/*
    public function __construct(ImageOptimizer $imageOptimizer)
    {
        $this->imageOptimizer = $imageOptimizer;
    }
*/

    public function onVichUploaderPostUpload(Event $event)
    {
        $object = $event->getObject();
        $mapping = $event->getMapping();

        // resize image and make thumbs
        if ($object instanceof Associate) {

            $imageOptimizer = new ImageOptimizer();

            if (!is_null($object->getImagePortraitFile())) {
                $imageOptimizer->resize($object->getImagePortraitFile()->getRealPath());
            }

            if (!is_null($object->getImageEntireFile())) {
                $imageOptimizer->resize($object->getImageEntireFile()->getRealPath());
            }
        }
    }
}