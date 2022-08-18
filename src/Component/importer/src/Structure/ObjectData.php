<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\Structure;

use Tulia\Component\Importer\Schema\ObjectDefinition;

/**
 * @author Adam Banaszkiewicz
 */
class ObjectData implements \ArrayAccess
{
    public function __construct(
        private array $objectData,
        private ObjectDefinition $definition
    ) {
    }

    public function toArray(): array
    {
        $result = [];

        foreach ($this->objectData as $field => $value) {
            if ($field[0] === '@') {
                continue;
            }

            if (is_array($value) && $this->definition->getField($field)->isCollection()) {
                foreach ($value as $k => $v) {
                    $value[$k] = $v->toArray();
                }
            }

            $result[$field] = $value;
        }

        return $result;
    }

    public function getObjectType(): string
    {
        return $this->objectData['@type'];
    }

    public function getObjectId(): string
    {
        return $this->objectData['@id'] ?? '';
    }

    public function getDefinition(): ObjectDefinition
    {
        return $this->definition;
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->objectData[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->objectData[$offset] ?? $this->definition->getField($offset)->getDefaultValue();
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->objectData[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->objectData[$offset]);
    }
}
