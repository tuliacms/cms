<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\ObjectExporter\ObjectsCollection;

use Traversable;

/**
 * @author Adam Banaszkiewicz
 */
final class ObjectsCollection implements \ArrayAccess, \IteratorAggregate
{
    private array $objects = [];

    public function addObject(ObjectToExport $object): void
    {
        $this->objects[] = $object;
    }

    public function getTypes(): array
    {
        return array_unique(array_map(static fn($v) => $v->type, $this->objects));
    }

    public function ofType(string $type): array
    {
        return array_filter($this->objects, static fn($v) => $v->type === $type);
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->objects[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->objects[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->objects[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->objects[$offset]);
    }

    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->objects);
    }
}
