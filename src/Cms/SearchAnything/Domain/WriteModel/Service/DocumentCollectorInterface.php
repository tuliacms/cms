<?php

declare(strict_types=1);

namespace Tulia\Cms\SearchAnything\Domain\WriteModel\Service;

/**
 * @author Adam Banaszkiewicz
 */
interface DocumentCollectorInterface
{
    public function collect(IndexInterface $index, ?string $websiteId, ?string $locale, int $offset, int $limit): void;

    public function countDocuments(string $websiteId, string $locale): int;
}
