<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Domain\ReadModel\Service;

use Tulia\Cms\Filemanager\Application\Service\ImageUrlResolver;
use Tulia\Cms\Filemanager\Domain\ReadModel\Finder\FileFinderInterface;
use Tulia\Cms\Filemanager\Domain\ReadModel\Finder\FileFinderScopeEnum;
use Tulia\Cms\Filemanager\Domain\WriteModel\Model\FileTypeEnum;

/**
 * @author Adam Banaszkiewicz
 */
final class ImageUrlGenerator implements ImageUrlGeneratorInterface
{
    public function __construct(
        private readonly FileFinderInterface $finder,
        private readonly ImageUrlResolver $urlResolver,
    ) {
    }

    public function generate(?string $id, array $params = []): string
    {
        if (empty($id)) {
            return $params['default'] ?? '';
        }

        $image = $this->finder->findOne([
            'id' => $id,
            'type' => FileTypeEnum::IMAGE,
        ], FileFinderScopeEnum::SINGLE);

        if ($image === null) {
            return $params['default'] ?? '';
        }

        return $this->urlResolver->size($image, $params['size'] ?? 'original');
    }
}
