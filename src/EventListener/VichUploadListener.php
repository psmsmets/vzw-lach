<?php

namespace App\EventListener;

use App\Entity\Associate;
use App\Service\ImageOptimizer;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Vich\UploaderBundle\Event\Event;

class VichUploadListener
{
    public function onVichUploaderPreUpload(Event $event)
    {
        $mapping = $event->getMapping();
        $destination = $mapping->getUploadDestination();
        $filesystem = new Filesystem();

        if (!$filesystem->exists($destination))
        {
            try {
                $filesystem->mkdir(
                    Path::normalize(sys_get_temp_dir().'/'.random_int(0, 1000)),
                );
            } catch (IOExceptionInterface $exception) {
                echo "An error occurred while creating your directory at ".$exception->getPath();
            }
        }
    }

    public function onVichUploaderPostUpload(Event $event)
    {
        $object = $event->getObject();
        // $mapping = $event->getMapping();

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
