<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Infrastructure\Persistence\Doctrine\Dbal;

use Tulia\Cms\Website\Domain\ReadModel\Finder\WebsiteFinderInterface;
use Tulia\Cms\Website\Domain\ReadModel\Model\Locale;
use Tulia\Cms\Website\Domain\ReadModel\Model\Website;
use Tulia\Cms\Website\Domain\WriteModel\Exception\WebsiteNotFoundException;

/**
 * @author Adam Banaszkiewicz
 */
final class DbalWebsiteFinder implements WebsiteFinderInterface
{
    private array $cache = [];

    public function __construct(
        private readonly CachedDbalWebsitesStorage $storage,
    ) {
    }

    public function get(string $id): Website
    {
        foreach ($this->all() as $website) {
            if ($website->getId() === $id) {
                return $website;
            }
        }

        throw WebsiteNotFoundException::fromId($id);
    }

    public function all(): array
    {
        if ($this->cache !== []) {
            return $this->cache;
        }

        $result = [];

        foreach ($this->storage->all() as $website) {
            $locales = [];

            foreach ($website['locales'] as $locale) {
                $locales[] = Locale::fromArray([
                    'code' => $locale['code'],
                    'domain' => $locale['domain'],
                    'development_domain' => $locale['domain_development'],
                    'locale_prefix' => $locale['locale_prefix'],
                    'path_prefix' => $locale['path_prefix'],
                    'ssl_mode' => $locale['ssl_mode'],
                    'is_default' => (bool) $locale['is_default'],
               ]);
            }

            $result[] = Website::fromArray([
                'id' => $website['id'],
                'locales' => $locales,
                'backend_prefix' => $website['backend_prefix'],
                'name' => $website['name'],
                'active' => (bool) $website['active'],
            ]);
        }

        return $this->cache = $result;
    }
}
