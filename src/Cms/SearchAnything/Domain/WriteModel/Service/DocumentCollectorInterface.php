<?php

declare(strict_types=1);

namespace Tulia\Cms\SearchAnything\Domain\WriteModel\Service;

use Tulia\Cms\SearchAnything\Domain\WriteModel\Model\Document;

/**
 * @author Adam Banaszkiewicz
 */
interface DocumentCollectorInterface
{
    /**
     * @return Document[]
     */
    public function collect(IndexInterface $index, string $locale, int $offset, int $limit): void;

    public function countDocuments(string $locale): int;

    public function getIndex(): string;
}
