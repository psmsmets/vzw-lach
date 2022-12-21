<?php

namespace App\Service;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;

class ImageOptimizer
{
    private const MAX_SIDE = 1920;
    private const MAX_THUMB = 250;
    private const DIR_THUMB = '/thumbs/';
    private const MIME_CONTENT_TYPES = array("image/jpeg", "image/png");

    private $imagine;

    public function __construct()
    {
        $this->imagine = new Imagine();

        // Use the Exif metadata reader to be able of accessing the orientation
        $this->imagine->setMetadataReader(new \Imagine\Image\Metadata\ExifMetadataReader());
    }

    public function resize(string $imageFile): void
    {
        // File exists?
        if (!file_exists($imageFile)) return;

        // Correct filetype?
        if (!in_array(mime_content_type($imageFile), self::MIME_CONTENT_TYPES)) return;

        // Does the thumb directory exist?
        $thumbDir = dirname($imageFile).self::DIR_THUMB;
        $thumbFile = $thumbDir.basename($imageFile);
        if (!file_exists($thumbDir)) mkdir($thumbDir, 0777, true);

        // Load the image into memory
        $image = $this->imagine->open($imageFile);

        // Use the autorotate filter to rotate the image if needed
        $filter = new \Imagine\Filter\Basic\Autorotate();
        $filter->apply($image);

        // Resize down while keeping aspect ratio
        $image = $image->thumbnail(
            new \Imagine\Image\Box(self::MAX_SIDE, self::MAX_SIDE),
            \Imagine\Image\ManipulatorInterface::THUMBNAIL_INSET
        );
        $image->strip()->save($imageFile);

        // Create thumbnail down while keeping aspect ratio
        $image = $image->thumbnail(
            new \Imagine\Image\Box(self::MAX_THUMB, self::MAX_THUMB),
            \Imagine\Image\ManipulatorInterface::THUMBNAIL_INSET
        );
        $image->strip()->save($thumbFile);
    }

    public function autorotate(string $imageFile): void
    {
        if (!file_exists($imageFile)) return;
        if (!in_array(mime_content_type($imageFile), self::MIME_CONTENT_TYPES)) return;

        // Load the image into memory
        $image = $this->imagine->open($imageFile);

        // Use the autorotate filter to rotate the image if needed
        $filter = new \Imagine\Filter\Basic\Autorotate();
        $filter->apply($image);

        // Strip off any metadata embedded in the image to save space and privacy
        $image->strip();

        // Save image to disk
        $image->save($imageFile);
    }
}
