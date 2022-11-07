<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Application\Service;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Tulia\Cms\Filemanager\Domain\ImageSize\ImageSizeRegistryInterface;
use Tulia\Cms\Filemanager\Domain\ReadModel\Model\File;

/**
 * @author Adam Banaszkiewicz
 * @todo Move this service do domain
 */
class ImageUrlResolver
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
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

        return $this->urlGenerator->generate('frontend.filemanager.resolve.image.size', [
            'size' => $size,
            'id'   => $image->getId(),
            'filename' => $image->getFilename(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    public function thumbnail(File $image): string
    {
        $size = $this->imageSize->get('thumbnail');

        return $this->urlGenerator->generate('frontend.filemanager.resolve.image.size', [
            'size' => $size->getCode(),
            'id'   => $image->getId(),
            'filename' => $image->getFilename(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);
    }
}
