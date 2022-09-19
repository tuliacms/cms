<?php

declare(strict_types=1);

namespace Tulia\Cms\SearchAnything\Domain\WriteModel\Service;

/**
 * @author Adam Banaszkiewicz
 */
interface IndexerInterface
{
    public function index(string $index, string $websiteId = '00000000-0000-0000-0000-000000000000', string $locale = 'unilingual'): IndexInterface;
}
