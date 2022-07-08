<?php

declare(strict_types=1);

namespace Tulia\Component\Datatable\Finder;

/**
 * @author Adam Banaszkiewicz
 */
final class FinderContext
{
    public function __construct(
        public readonly string $locale,
        public readonly string $defaultLocale,
    ) {
    }

    public function isDefaultLocale(): bool
    {
        return $this->locale === $this->defaultLocale;
    }
}
