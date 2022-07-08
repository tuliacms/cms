<?php

declare(strict_types=1);

namespace Tulia\Cms\Options\Domain\ReadModel;

use Tulia\Cms\Options\Domain\WriteModel\OptionsRepositoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Options
{
    private OptionsFinderInterface $finder;
    private OptionsRepositoryInterface $repository;
    private array $cache = [];
    private array $autoloaded = [];

    public function __construct(
        OptionsFinderInterface $finder,
        OptionsRepositoryInterface $repository,
    ) {
        $this->finder = $finder;
        $this->repository = $repository;
    }

    public function get(string $name, mixed $default = null, ?string $locale = null): mixed
    {
        $locale = $this->resolveLocale($locale);

        $this->autoload($locale);

        if ($this->existsInCache($locale, $name)) {
            return $this->cache[$locale][$name];
        }

        $value = $this->finder->findByName($name, $locale);

        if (empty($value)) {
            $value = $default;
        }

        return $this->cache[$locale][$name] = $value;
    }

    public function preload(array $names, ?string $locale = null): void
    {
        $locale = $this->resolveLocale($locale);

        $values = $this->finder->findBulkByName(
            $names,
            $locale
        );

        foreach ($values as $name => $value) {
            $this->cache[$locale][$name] = $value;
        }
    }

    private function resolveLocale(?string $locale = null): string
    {
        if (! $locale) {
            return 'en_US';//$this->currentWebsite->getLocale()->getCode();
        }

        return $locale;
    }

    private function autoload(string $locale): void
    {
        if (isset($this->autoloaded[$locale])) {
            return;
        }

        $this->cache[$locale] = $this->finder->autoload($locale);
        $this->autoloaded[$locale] = true;
    }

    private function existsInCache(string $locale, string $name): bool
    {
        return isset($this->cache[$locale]) && \array_key_exists($name, $this->cache[$locale]);
    }
}
