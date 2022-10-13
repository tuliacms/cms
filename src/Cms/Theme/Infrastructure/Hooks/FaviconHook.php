<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Infrastructure\Hooks;

use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Tulia\Cms\Filemanager\Application\Service\ImageUrlResolver;
use Tulia\Cms\Filemanager\Domain\ReadModel\Finder\FileFinderInterface;
use Tulia\Cms\Filemanager\Domain\ReadModel\Finder\FileFinderScopeEnum;
use Tulia\Cms\Filemanager\Domain\WriteModel\Model\FileTypeEnum;
use Tulia\Cms\Options\Domain\ReadModel\OptionsFinderInterface;
use Tulia\Component\Hooks\HooksSubscriberInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 * @final
 */
class FaviconHook implements HooksSubscriberInterface
{
    public function __construct(
        private readonly OptionsFinderInterface $options,
        private readonly FileFinderInterface $fileFinder,
        private readonly ImageUrlResolver $imageUrlResolver,
        private readonly TagAwareCacheInterface $settingsCache,
    ) {
    }

    public static function getSubscribedActions(): array
    {
        return [
            'theme.head' => 'registerFavicon',
        ];
    }

    public function registerFavicon(WebsiteInterface $website): string
    {
        if ($website->isBackend()) {
            return '';
        }

        $key = sprintf('website_favicon_%s_%s', $website->getId(), $website->getLocale()->getCode());

        return $this->settingsCache->get($key, function (ItemInterface $item) use ($website) {
            $item->tag('settings');

            $fileId = $this->options->findByName('website_favicon', $website->getId(), $website->getLocale()->getCode());

            if (!$fileId) {
                return '';
            }

            $image = $this->fileFinder->findOne([
                'id' => $fileId,
                'type' => FileTypeEnum::IMAGE,
            ], FileFinderScopeEnum::SINGLE);

            $url = $this->imageUrlResolver->size($image, 'original');

            $contentType = match(pathinfo($url, PATHINFO_EXTENSION)) {
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'apng' => 'image/apng',
                'ico' => 'image/x-icon',
                default => 'image/x-icon',
            };

            return PHP_EOL.sprintf('<link rel="icon" type="%s" href="%s">', $contentType, $url);
        });
    }
}
