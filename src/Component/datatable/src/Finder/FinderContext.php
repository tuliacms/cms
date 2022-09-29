<?php

declare(strict_types=1);

namespace Tulia\Component\Datatable\Finder;

use ArrayAccess;

/**
 * @author Adam Banaszkiewicz
 */
final class FinderContext implements ArrayAccess
{
    public function __construct(
        public readonly array $context,
    ) {
    }

    public function offsetExists(mixed $offset): bool
    {
        return \array_key_exists($offset, $this->context);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->context[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->context[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->context[$offset]);
    }
}
