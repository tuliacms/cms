<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Attributes\Domain\WriteModel\Model;

/**
 * @author Adam Banaszkiewicz
 * @final
 */
class AttributeValue implements \Stringable, \Traversable, \ArrayAccess, \IteratorAggregate
{
    private readonly array $values;

    public function __construct(array $values)
    {
        // Remove empty values
        $values = array_filter($values);
        $this->values = $values;
    }

    public function __toString(): string
    {
        return implode(', ', $this->values);
    }

    public function toRaw(): array
    {
        return $this->values;
    }

    public function isEmpty(): bool
    {
        return empty($this->values);
    }

    public function getIterator(): \Iterator
    {
        return new \ArrayIterator($this->values);
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->values[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->values[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new \LogicException('Cannot change value of Value Object.');
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new \LogicException('Cannot change value of Value Object.');
    }
}
