<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Application\Service;

use Tulia\Cms\Filemanager\Domain\ImageSize\ImageSize;
use Tulia\Cms\Filemanager\Domain\ImageSize\ImageSizeRegistryInterface;
use Tulia\Cms\Filemanager\Domain\ReadModel\Model\File;
use Tulia\Cms\Filemanager\Domain\WriteModel\Model\FileTypeEnum;
use Tulia\Component\Image\ImageInterface;
use Tulia\Component\Image\ImageManagerInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Cropper
{
    public function __construct(
        private readonly ImageManagerInterface $imageManager,
        private readonly ImageSizeRegistryInterface $imageSize,
        private readonly string $filesDirectory,
    ) {
    }

    public function crop(File $image, string $sizeName): string
    {
        if ($image->getType() !== FileTypeEnum::IMAGE) {
            throw new \InvalidArgumentException(sprintf('First argument of crop() method must be an image, "%s" given.', $image->getType()));
        }

        @ [$sizeName, $sizeDetails] = explode('_', $sizeName);

        if ($this->imageSize->has($sizeName) === false) {
            throw new \InvalidArgumentException(sprintf('Image size "%s" not found in registered sizes.', $sizeName));
        }

        $size = $this->imageSize->get($sizeName);

        $directory = substr($image->getId(), 0, 2) . '/' . substr($image->getId(), 2, 2) . '/' . substr($image->getId(), 4, 2) . '/' . $image->getId();
        $name = pathinfo($image->getFilename(), PATHINFO_FILENAME);

        $source = $this->filesDirectory . '/' . $image->getPath() . '/' . $image->getFilename();
        $output = '/uploads/thumbnails/' . $size->getCode() . '/' . $directory . '/' . $name . '.' . $image->getExtension();

        // Return path, if file already exists.
        if (is_file($this->filesDirectory . $output)) {
            return $output;
        }

        if (is_dir(\dirname($this->filesDirectory . $output)) === false) {
            if (!mkdir($concurrentDirectory = \dirname($this->filesDirectory . $output), 0777, true) && !is_dir($concurrentDirectory)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
            }
        }

        $img = $this->imageManager->make($source);
        $this->cropMode($img, $size);
        $img->save($this->filesDirectory . $output);

        return $output;
    }

    private function cropMode(ImageInterface $image, ImageSize $size): void
    {
        if ($size->getMode() === 'fit') {
            $image->fit($size->getWidth(), $size->getHeight());
        } elseif ($size->getMode() === 'widen') {
            $image->widen($size->getWidth());
        } elseif ($size->getMode() === 'heighten') {
            $image->heighten($size->getWidth());
        } elseif ($size->getMode() === 'resize') {
            $image->resize($size->getWidth(), $size->getHeight(), function ($constraint) {
                $constraint->aspectRatio();
            });
        }
    }
}
