<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Infrastructure\Framework\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tulia\Cms\Platform\Domain\Service\DynamicConfigurationInterface;
use Tulia\Cms\Website\Domain\ReadModel\Finder\WebsiteFinderInterface;
use Tulia\Cms\Website\Domain\ReadModel\Finder\WebsiteFinderScopeEnum;
use Tulia\Cms\Website\Domain\ReadModel\Model\Website;
use Tulia\Cms\Website\Domain\WriteModel\Event\WebsiteCreated;
use Tulia\Cms\Website\Domain\WriteModel\Event\WebsiteDeleted;
use Tulia\Cms\Website\Domain\WriteModel\Event\WebsiteUpdated;

/**
 * @author Adam Banaszkiewicz
 */
final class UpdateWebsitesDynamicConfigWhenAnyWebsiteChanges implements EventSubscriberInterface
{
    public function __construct(
        private readonly WebsiteFinderInterface $finder,
        private readonly DynamicConfigurationInterface $configuration
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            WebsiteUpdated::class => 'update',
            WebsiteCreated::class => 'update',
            WebsiteDeleted::class => 'update',
        ];
    }

    public function update(): void
    {
        $websites = $this->finder->find(['active' => 1], WebsiteFinderScopeEnum::INTERNAL);

        $this->configuration->open();
        $this->configuration->set('cms.websites', $this->createWebsitesCollection($websites->toArray()));
        $this->configuration->write();
    }

    private function createWebsitesCollection(array $source): array
    {
        $result = [];

        /** @var Website $website */
        foreach ($source as $website) {
            $locales = [];

            foreach ($website->getLocales() as $locale) {
                $locales[] = [
                    'locale_code' => $locale->getCode(),
                    'domain' => $locale->getDomain(),
                    'domain_development' => $locale->getDevelopmentDomain(),
                    'path_prefix' => $locale->getPathPrefix(),
                    'locale_prefix' => $locale->getLocalePrefix(),
                    'default' => $locale->isDefault(),
                    'ssl_mode' => $locale->getSslMode(),
                ];
            }

            $result[] = [
                'id' => $website->getId(),
                'backend_prefix' => $website->getBackendPrefix(),
                'locales' => $locales,
            ];
        }

        return $result;
    }
}
