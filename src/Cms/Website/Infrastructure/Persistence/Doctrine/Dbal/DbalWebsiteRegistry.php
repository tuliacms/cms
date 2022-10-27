<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Infrastructure\Persistence\Doctrine\Dbal;

use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\SslModeEnum;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\Locale;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\Website;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteRegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class DbalWebsiteRegistry implements WebsiteRegistryInterface
{
    private array $cache = [];

    public function __construct(
        private readonly CachedDbalWebsitesStorage $storage,
        private readonly string $environment,
    ) {
    }

    public function all(): array
    {
        if ($this->cache !== []) {
            return $this->cache;
        }

        $result = [];

        foreach ($this->storage->all() as $website) {
            $websiteLocales = $this->collectLocalesForWebsite($website['locales']);
            $defaultLocaleCode = $this->findDefaultLocale($website['id'], $website['locales']);

            $result[] = new Website(
                id: $website['id'],
                name: $website['name'],
                backendPrefix: $website['backend_prefix'],
                isBackend: false,
                locales: $websiteLocales,
                defaultLocale: $defaultLocaleCode,
                activeLocale: $defaultLocaleCode,
                enabled: (bool) $website['enabled'],
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

    private function collectLocalesForWebsite(array $locales): array
    {
        $result = [];

        foreach ($locales as $locale) {
            $result[] = new Locale(
                $locale['code'],
                $this->environment === 'dev'
                    ? $locale['domain_development']
                    : $locale['domain'],
                $locale['locale_prefix'],
                $locale['path_prefix'],
                SslModeEnum::from($locale['ssl_mode']),
            );
        }

        return $result;
    }

    private function findDefaultLocale(string $websiteId, array $locales): string
    {
        foreach ($locales as $locale) {
            if ($locale['is_default']) {
                return $locale['code'];
            }
        }

        throw new \DomainException(sprintf('Missing default locale for website %s', $websiteId));
    }
}
