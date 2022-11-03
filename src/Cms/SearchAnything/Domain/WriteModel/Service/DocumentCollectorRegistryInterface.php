<?php

declare(strict_types=1);

namespace Tulia\Cms\SearchAnything\Domain\WriteModel\Service;

/**
 * @author Adam Banaszkiewicz
 */
interface DocumentCollectorRegistryInterface
{
    /**
     * @return DocumentCollectorInterface[]
     */
    public function all(): array;

    public function get(string $name): DocumentCollectorInterface;
}
