<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\Structure;

use Symfony\Component\Filesystem\Filesystem;
use Tulia\Component\Importer\Schema\ObjectDefinition;

/**
 * @author Adam Banaszkiewicz
 */
class ObjectData implements \ArrayAccess
{
    public function __construct(
        private array $objectData,
        private readonly ObjectDefinition $definition,
        private readonly string $importRootPath,
    ) {
    }

    public function toArray(): array
    {
        $result = [];

        foreach ($this->objectData as $field => $value) {
            if (
                $this->definition->hasField($field)
                && is_array($value)
                && $this->definition->getField($field)->isCollection()
            ) {
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

    public function withNewId(string $id): self
    {
        $clone = clone $this;
        $clone['@id'] = $id;

        return $clone;
    }

    public function getDefinition(): ObjectDefinition
    {
        return $this->definition;
    }

    public function getImportRootPath(): string
    {
        return $this->importRootPath;
    }

    public function getFilepathOf(string $key): ?string
    {
        if (!isset($this->objectData[$key])) {
            return null;
        }

        $path = $this->objectData[$key];

        if ((new Filesystem())->isAbsolutePath($path)) {
            return $path;
        }

        if (strncmp($path, './', 2) === 0) {
            $path = substr($path, 2);
        }

        $path = ltrim($path, '/');

        return $this->importRootPath.'/'.$path;
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
