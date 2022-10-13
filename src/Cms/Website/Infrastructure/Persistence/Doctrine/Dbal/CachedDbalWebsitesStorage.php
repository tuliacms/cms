<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Infrastructure\Persistence\Doctrine\Dbal;

use Doctrine\DBAL\Connection;

/**
 * @author Adam Banaszkiewicz
 */
final class CachedDbalWebsitesStorage
{
    private array $cache = [];

    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    public function all(): array
    {
        if ($this->cache !== []) {
            return $this->cache;
        }

        $sourceWebsites = $this->connection->fetchAllAssociative('SELECT *, BIN_TO_UUID(id) AS id FROM #__website');
        $sourceLocales = $this->connection->fetchAllAssociative('SELECT *, BIN_TO_UUID(id) AS id, BIN_TO_UUID(website_id) AS website_id FROM #__website_locale');

        $result = [];

        foreach ($sourceWebsites as $website) {
            $locales = [];

            foreach ($sourceLocales as $locale) {
                if ($locale['website_id'] === $website['id']) {
                    $locales[] = $locale;
                }
            }

            $website['locales'] = $locales;
            $result[] = $website;
        }

        return $this->cache = $result;
    }
}
