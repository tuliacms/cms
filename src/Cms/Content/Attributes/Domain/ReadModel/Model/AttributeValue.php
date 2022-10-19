<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Attributes\Domain\ReadModel\Model;

/**
 * @author Adam Banaszkiewicz
 */
class AttributeValue implements \Stringable, \ArrayAccess, \IteratorAggregate, \Countable
{
    private array $values = [];
    private bool $isRepeatable = false;

    public function __construct(mixed $values)
    {
        if (is_array($values) === false) {
            $values = [$values];
        }

        if ($values === []) {
            return;
        }

        foreach ($values as $key => $value) {
            if (is_array($value)) {
                $this->isRepeatable = true;
                $this->values[$key] = new AttributeValue($value);
            } else {
                $this->values[$key] = $value;
            }
        }
    }

    public function isRepeatable(): bool
    {
        return $this->isRepeatable;
    }

    public function isEmpty(): bool
    {
        if ($this->values === []) {
            return true;
        }

        if (count($this->values) === 1 && empty(reset($this->values))) {
            return true;
        }

        return false;
    }

    public function __toString(): string
    {
        return implode(', ', $this->values);
    }

    public function count(): int
    {
        return count($this->values);
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->values[$offset]);
    }

    /**
     * @return mixed
     */
    public function offsetGet(mixed $offset)
    {
        return $this->values[$offset];
    }

    public function offsetSet(mixed $offset, $value): void
    {
        $this->values[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->values[$offset]);
    }

    public function getIterator(): \Iterator
    {
        if ($this->count() === 1 && $this->isRepeatable()) {
            return new \ArrayIterator(reset($this->values)->values);
        }

        return new \ArrayIterator($this->values);
    }
}
