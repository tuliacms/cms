<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Domain;

/**
 * @author Adam Banaszkiewicz
 */
final class ThemeImportCollectionRegistry
{
    private array $collections = [];

    public function addCollection(string $theme, string $code, array $collection): void
    {
        if (!isset($this->collections[$theme])) {
            $this->collections[$theme] = [];
        }

        $this->collections[$theme][$code] = $collection;
    }

    public function hasAny(string $theme): bool
    {
        return isset($this->collections[$theme]);
    }

    public function getFor(string $theme): array
    {
        return $this->collections[$theme];
    }
}
