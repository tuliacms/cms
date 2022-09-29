<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Infrastructure\Persistence\LocalStorage;

use Tulia\Cms\Website\Domain\ReadModel\Finder\WebsiteFinderInterface;
use Tulia\Component\Routing\Enum\SslModeEnum;
use Tulia\Component\Routing\Website\Locale\Locale;
use Tulia\Component\Routing\Website\Website;
use Tulia\Component\Routing\Website\WebsiteInterface;
use Tulia\Component\Routing\Website\WebsiteRegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class LocalStorageWebsiteRegistry implements WebsiteRegistryInterface
{
    private array $cache = [];

    public function __construct(
        private readonly WebsiteFinderInterface $finder,
        private readonly string $environment,
    ) {
    }

    public function all(): array
    {
        if ($this->cache !== []) {
            return $this->cache;
        }

        $websites = $this->finder->all();
        $result = [];

        foreach ($websites as $website) {
            $websiteLocales = $this->collectLocalesForWebsite($website->getLocales());
            $defaultLocaleCode = $this->findDefaultLocale($website->getId(), $website->getLocales());

            $result[] = new Website(
                id: $website->getId(),
                name: $website->getName(),
                backendPrefix: $website->getBackendPrefix(),
                isBackend: false,
                basepath: '',
                locales: $websiteLocales,
                defaultLocale: $defaultLocaleCode,
                activeLocale: $defaultLocaleCode,
                active: $website->isActive(),
            );
        }

        return $this->cache = $result;
    }

    public function get(string $id): WebsiteInterface
    {
        foreach ($this->all() as $website) {
            if ($website->getId() === $id) {
                return $website;
            }
        }

        throw new \Exception(sprintf('Website "%s" not exists.', $id));
    }

    /**
     * @param \Tulia\Cms\Website\Domain\ReadModel\Model\Locale[] $locales
     */
    private function collectLocalesForWebsite(array $locales): array
    {
        $result = [];

        foreach ($locales as $locale) {
            $result[] = new Locale(
                $locale->getCode(),
                $this->environment === 'dev'
                    ? $locale->getDevelopmentDomain()
                    : $locale->getDomain(),
                $locale->getLocalePrefix(),
                $locale->getPathPrefix(),
                SslModeEnum::from($locale->getSslMode()),
            );
        }

        return $result;
    }

    /**
     * @param \Tulia\Cms\Website\Domain\ReadModel\Model\Locale[] $locales
     */
    private function findDefaultLocale(string $websiteId, array $locales): string
    {
        foreach ($locales as $locale) {
            if ($locale->isDefault()) {
                return $locale->getCode();
            }
        }

        throw new \DomainException(sprintf('Missing default locale for website %s', $websiteId));
    }
}
