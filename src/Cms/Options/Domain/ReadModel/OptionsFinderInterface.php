<?php

declare(strict_types=1);

namespace Tulia\Cms\Options\Domain\ReadModel;

/**
 * @author Adam Banaszkiewicz
 */
interface OptionsFinderInterface
{
    public function findByName(string $name, string $websiteId, string $locale);

    public function findBulkByName(array $names, string $websiteId, string $locale): array;

    public function autoload(string $websiteId, string $locale): array;
}
