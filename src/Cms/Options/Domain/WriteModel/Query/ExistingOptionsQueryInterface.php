<?php

declare(strict_types=1);

namespace Tulia\Cms\Options\Domain\WriteModel\Query;

/**
 * @author Adam Banaszkiewicz
 */
interface ExistingOptionsQueryInterface
{
    /**
     * @return string[]
     */
    public function collectNames(string $websiteId): array;
}
