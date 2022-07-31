<?php

declare(strict_types=1);

namespace Tulia\Cms\SearchAnything\Domain\WriteModel\Service;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractDocumentCollector implements DocumentCollectorInterface
{
    public function isMultilingual(): bool
    {
        return true;
    }
}
