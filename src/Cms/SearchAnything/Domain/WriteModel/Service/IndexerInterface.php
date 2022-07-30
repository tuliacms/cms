<?php

declare(strict_types=1);

namespace Tulia\Cms\SearchAnything\Domain\WriteModel\Service;

/**
 * @author Adam Banaszkiewicz
 */
interface IndexerInterface
{
    public function index(string $index, string $locale): IndexInterface;
}
