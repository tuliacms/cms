<?php

declare(strict_types=1);

namespace Tulia\Cms\SearchAnything\Domain\ReadModel\Query;

/**
 * @author Adam Banaszkiewicz
 */
interface SearchEngineInterface
{
    public function search(string $query, string $locale, int $limit, int $offset): array;
}
