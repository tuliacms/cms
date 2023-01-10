<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Domain\ReadModel\Finder\Model;

/**
 * @author Adam Banaszkiewicz
 */
class Collection implements \ArrayAccess, \IteratorAggregate
{
    protected array $elements = [];
    protected int $totalFound = 0;
    protected $totalFoundCallback;

    public function __construct(array $elements = [], callable $totalFoundCallback = null)
    {
        $this->replace($elements);
        $this->totalFoundCallback = $totalFoundCallback ?? static fn () => 0;
    }

    public function toArray(): array
    {
        return $this->elements;
    }

    public function append(mixed $element): void
    {
        $this->elements[] = $element;
    }

    public function merge(self $collection): void
    {
        foreach ($collection as $element) {
            $this->append($element);
        }
    }

    public function replace(array $elements): void
    {
        $this->elements = [];

        foreach ($elements as $element) {
            $this->append($element);
        }
    }

    public function count(): int
    {
        return count($this->elements);
    }

    public function totalRows(): int
    {
        return (int) \call_user_func($this->totalFoundCallback);
    }

    public function first(): mixed
    {
        return $this->elements[0] ?? null;
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
