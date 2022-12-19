<?php

namespace App\Service;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;

class ImageOptimizer
{
    private const MAX_WIDTH = 1200;
    private const MAX_HEIGHT = 1800;
    private const MAX_THUMB = 250;
    private const DIR_THUMB = '/thumbs/';
    private const MIME_CONTENT_TYPES = array("image/jpeg", "image/png");

    private $imagine;

    public function __construct()
    {
        $this->imagine = new Imagine();
    }

    public function resize(string $imageFile): void
    {
        $imageDir = dirname($imageFile).self::DIR_THUMB;
        if (!file_exists($imageDir)) mkdir($imageDir, 0777, true);
        $this->resize_image($imageFile, self::MAX_WIDTH, self::MAX_HEIGHT);
        $this->create_thumb($imageFile);
    }

    public function resize_image(string $imageFile, int $width = self::MAX_WIDTH, int $height = self::MAX_HEIGHT): void
    {
        if (!file_exists($imageFile)) return;
        if (!in_array(mime_content_type($imageFile), self::MIME_CONTENT_TYPES)) return;

        list($iwidth, $iheight) = getimagesize($imageFile);

        if ($iwidth < $width) return;
        if ($iheight < $height) return;

        $ratio = $iwidth / $iheight;

        if ($width / $height > $ratio) {
            $width = $height * $ratio;
        } else {
            $height = $width / $ratio;
        }

        $photo = $this->imagine->open($imageFile);
        $photo->resize(new Box($width, $height))->save($imageFile);
    }

    public function create_thumb(string $imageFile): void
    {
        if (!file_exists($imageFile)) return;
        if (!in_array(mime_content_type($imageFile), self::MIME_CONTENT_TYPES)) return;

        $thumbFile = dirname($imageFile).self::DIR_THUMB.basename($imageFile);
        if (file_exists($thumbFile)) return;

        list($iwidth, $iheight) = getimagesize($imageFile);

        if ($iwidth < self::MAX_THUMB) return;
        if ($iheight < self::MAX_THUMB) return;

        $ratio = $iwidth / $iheight;
        $width = self::MAX_THUMB;
        $height = self::MAX_THUMB;

        if ($width / $height > $ratio) {
            $width = $height * $ratio;
        } else {
            $height = $width / $ratio;
        }

        $photo = $this->imagine->open($imageFile);
        $photo->resize(new Box($width, $height))->save($thumbFile);
    }
}
