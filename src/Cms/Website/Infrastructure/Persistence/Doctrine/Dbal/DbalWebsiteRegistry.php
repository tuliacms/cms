<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Infrastructure\Persistence\Doctrine\Dbal;

use Doctrine\DBAL\Connection;
use Tulia\Component\Routing\Enum\SslModeEnum;
use Tulia\Component\Routing\Website\Locale\Locale;
use Tulia\Component\Routing\Website\Website;
use Tulia\Component\Routing\Website\WebsiteRegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class DbalWebsiteRegistry implements WebsiteRegistryInterface
{
    public function __construct(
        private readonly Connection $connection
    ) {
    }

    public function all(): array
    {
        $websites = $this->connection->fetchAllAssociative('SELECT *, BIN_TO_UUID(id) AS id FROM #__website');
        $locales = $this->connection->fetchAllAssociative('SELECT *, BIN_TO_UUID(website_id) AS website_id FROM #__website_locale');
        $result = [];

        foreach ($websites as $row) {
            $websiteLocales = $this->collectLocalesForWebsite($row['id'], $locales);
            $defaultLocaleCode = $this->findDefaultLocale($row['id'], $locales);

            $result[] = new Website(
                id: $row['id'],
                name: $row['name'],
                backendPrefix: $row['backend_prefix'],
                isBackend: false,
                basepath: '',
                locales: $websiteLocales,
                defaultLocale: $defaultLocaleCode,
                activeLocale: $defaultLocaleCode,
                active: (bool) $row['active'],
            );
        }

        return $result;
    }

    private function collectLocalesForWebsite(string $websiteId, array $locales): array
    {
        $result = [];

        foreach ($locales as $locale) {
            if ($locale['website_id'] === $websiteId) {
                $result[] = new Locale(
                    $locale['code'],
                    $locale['domain'],
                    $locale['locale_prefix'],
                    $locale['path_prefix'],
                    SslModeEnum::from($locale['ssl_mode']),
                );
            }
        }

        return $result;
    }

    private function findDefaultLocale(string $websiteId, array $locales): string
    {
        foreach ($locales as $locale) {
            if ($locale['website_id'] === $websiteId) {
                return $locale['code'];
            }
        }

        throw new \DomainException(sprintf('Missing default locale for website %s', $websiteId));
    }
}
