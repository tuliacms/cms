<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Application\Service;

use Tulia\Cms\Filemanager\Domain\ImageSize\ImageSizeRegistryInterface;
use Tulia\Cms\Filemanager\Domain\ReadModel\Model\File;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ImageUrlResolver
{
    public function __construct(
        private readonly RouterInterface $router,
        private readonly ImageSizeRegistryInterface $imageSize,
    ) {
    }

    public function size(File $image, string $sizeName): string
    {
        if ($sizeName === 'original') {
            $size = $sizeName;
        } else {
            $size = $this->imageSize->get($sizeName)->getCode();
        }

        return $this->router->generate('filemanager.resolve.image.size', [
            'size' => $size,
            'id'   => $image->getId(),
            'filename' => $image->getFilename(),
        ]);
    }

    public function thumbnail(File $image): string
    {
        $size = $this->imageSize->get('thumbnail');

        return $this->router->generate('filemanager.resolve.image.size', [
            'size' => $size->getCode(),
            'id'   => $image->getId(),
            'filename' => $image->getFilename(),
        ]);
    }
}
