<?php

declare(strict_types=1);

namespace Tulia\Cms\Options\Domain\ReadModel;

use Tulia\Cms\Options\Domain\WriteModel\OptionsRepositoryInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Options
{
    private array $cache = [];
    private array $autoloaded = [];

    public function __construct(
        private readonly OptionsFinderInterface $finder,
        private readonly OptionsRepositoryInterface $repository,
        private readonly WebsiteInterface $website,
    ) {
    }

    public function get(
        string $name,
        mixed $default = null,
        ?string $websiteId = null,
        ?string $locale = null,
    ): mixed {
        $websiteId = $this->resolveWebsite($websiteId);
        $locale = $this->resolveLocale($locale);

        $this->autoload($websiteId, $locale);

        if ($this->existsInCache($websiteId, $locale, $name)) {
            return $this->cache[$websiteId][$locale][$name];
        }

        $value = $this->finder->findByName($name, $websiteId, $locale);

        if (empty($value)) {
            $value = $default;
        }

        return $this->cache[$websiteId][$locale][$name] = $value;
    }

    public function preload(array $names, ?string $websiteId = null, ?string $locale = null): void
    {
        $websiteId = $this->resolveWebsite($websiteId);
        $locale = $this->resolveLocale($locale);

        $values = $this->finder->findBulkByName($names, $websiteId, $locale);

        foreach ($values as $name => $value) {
            $this->cache[$websiteId][$locale][$name] = $value;
        }
    }

    private function autoload(?string $websiteId = null, ?string $locale = null): void
    {
        $websiteId = $this->resolveWebsite($websiteId);
        $locale = $this->resolveLocale($locale);

        if (isset($this->autoloaded[$websiteId][$locale])) {
            return;
        }

        $this->cache[$websiteId][$locale] = $this->finder->autoload($websiteId, $locale);
        $this->autoloaded[$websiteId][$locale] = true;
    }

    private function existsInCache(string $websiteId, string $locale, string $name): bool
    {
        return isset($this->cache[$websiteId][$locale]) && \array_key_exists($name, $this->cache[$websiteId][$locale]);
    }

    private function resolveLocale(?string $locale): string
    {
        return $locale ?? $this->website->getLocale()->getCode();
    }

    private function resolveWebsite(?string $websiteId): string
    {
        return $websiteId ?? $this->website->getId();
    }
}
