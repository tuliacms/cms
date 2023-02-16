<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\Builder\Hierarchy;

use Tulia\Cms\Menu\Domain\Builder\Criteria;

/**
 * @author Adam Banaszkiewicz
 */
class Hierarchy implements HierarchyInterface
{
    private array $elements = [];

    public function __construct(
        private readonly string $id,
        private readonly string $cacheKey,
    ) {
    }

    public static function cacheKeyFromCriteria(Criteria $criteria): string
    {
        return sprintf('menu_hierarchy_%s_%s_%s_%s', $criteria->websiteId, $criteria->locale, $criteria->by, $criteria->value);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function append(Item $item): void
    {
        $this->elements[] = $item;
    }

    public function getCacheKey(): string
    {
        return $this->cacheKey;
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->elements);
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->elements[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->elements[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if ($offset !== null) {
            $this->elements[$offset] = $value;
        } else {
            $this->elements[] = $value;
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->elements[$offset]);
    }
}
