<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\Structure;

/**
 * @author Adam Banaszkiewicz
 * @property mixed $value
 */
final class ObjectPropertyAccessor
{
    public function __construct(
        private readonly ObjectData $object,
        private readonly string $field,
        private readonly string $pattern,
    ) {
    }

    public function replaceWith(string $replacement): void
    {
        $this->value = str_replace($this->pattern, $replacement, $this->value);
    }

    public function __get(string $name): mixed
    {
        return $this->object[$this->field];
    }

    public function __set(string $name, mixed $value): void
    {
        $this->object[$this->field] = $value;
    }

    public function __isset(string $name): bool
    {
        return true;
    }
}
