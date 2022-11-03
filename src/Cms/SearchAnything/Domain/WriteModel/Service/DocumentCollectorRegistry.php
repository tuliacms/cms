<?php

declare(strict_types=1);

namespace Tulia\Cms\SearchAnything\Domain\WriteModel\Service;

/**
 * @author Adam Banaszkiewicz
 */
final class DocumentCollectorRegistry implements DocumentCollectorRegistryInterface
{
    public function __construct(
        private readonly \Traversable $collectors,
    ) {
    }

    public function all(): array
    {
        return iterator_to_array($this->collectors);
    }

    public function get(string $name): DocumentCollectorInterface
    {
        foreach ($this->collectors as $collector) {
            if ($collector instanceof $name) {
                return $collector;
            }
        }

        throw new \OutOfBoundsException(sprintf('Cannot find %s collector.', $name));
    }
}
