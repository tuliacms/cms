<?php

declare(strict_types=1);

namespace Tulia\Cms\SearchAnything\Domain\WriteModel\Service;

/**
 * @author Adam Banaszkiewicz
 */
final class DocumentCollectorRegistry implements DocumentCollectorRegistryInterface
{
    public function __construct(
        private \Traversable $collectors
    ) {
    }

    public function all(): array
    {
        return iterator_to_array($this->collectors);
    }
}
