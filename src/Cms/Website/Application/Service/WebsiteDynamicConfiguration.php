<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Application\Service;

use Tulia\Cms\Platform\Domain\Service\DynamicConfigurationInterface;
use Tulia\Cms\Website\Domain\ReadModel\Finder\WebsiteFinderInterface;
use Tulia\Cms\Website\Domain\ReadModel\Finder\WebsiteFinderScopeEnum;
use Tulia\Cms\Website\Domain\ReadModel\Model\Website;

/**
 * @author Adam Banaszkiewicz
 */
final class WebsiteDynamicConfiguration
{
    public function __construct(
        private readonly WebsiteFinderInterface $finder,
        private readonly DynamicConfigurationInterface $configuration
    ) {
    }

    public function update(): void
    {
        $websites = $this->finder->find([], WebsiteFinderScopeEnum::INTERNAL);

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
                'active' => $website->isActive(),
                'name' => $website->getName(),
                'locales' => $locales,
            ];
        }

        return $result;
    }
}
