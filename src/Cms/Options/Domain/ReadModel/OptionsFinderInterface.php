<?php

declare(strict_types=1);

namespace Tulia\Cms\Options\Domain\ReadModel;

/**
 * @author Adam Banaszkiewicz
 */
interface OptionsFinderInterface
{
    public function findByName(string $name, string $locale);

    public function findBulkByName(array $names, string $locale): array;

    public function autoload(string $locale): array;
}
