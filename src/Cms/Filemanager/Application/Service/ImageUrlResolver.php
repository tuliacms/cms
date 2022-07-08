<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Application\Service;

use Tulia\Cms\Filemanager\Domain\ImageSize\ImageSizeRegistryInterface;
use Tulia\Cms\Filemanager\Domain\ReadModel\Model\File;
use Symfony\Component\Routing\RouterInterface;
use Tulia\Component\Routing\Website\WebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ImageUrlResolver
{
    private RouterInterface $router;
    private ImageSizeRegistryInterface $imageSize;

    public function __construct(RouterInterface $router, ImageSizeRegistryInterface $imageSize)
    {
        $this->router = $router;
        $this->imageSize = $imageSize;
    }

    public function size(File $image, string $sizeName, WebsiteInterface $website): string
    {
        $size = $this->imageSize->get($sizeName);

        return $this->router->generate('filemanager.resolve.image.size', [
            'size' => $size->getCode(),
            'id'   => $image->getId(),
            'filename' => $image->getFilename(),
            'website' => $website,
        ]);
    }

    public function thumbnail(File $image, WebsiteInterface $website): string
    {
        $size = $this->imageSize->get('thumbnail');

        return $this->router->generate('filemanager.resolve.image.size', [
            'size' => $size->getCode(),
            'id'   => $image->getId(),
            'filename' => $image->getFilename(),
            'website' => $website,
        ]);
    }
}
