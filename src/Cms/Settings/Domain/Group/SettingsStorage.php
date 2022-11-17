<?php

declare(strict_types=1);

namespace Tulia\Cms\Settings\Domain\Group;

/**
 * @author Adam Banaszkiewicz
 */
final class SettingsStorage implements \ArrayAccess
{
    public function __construct(
        public readonly array $options,
    ) {
    }

    public function get(string $name, mixed $default = null): mixed
    {
        return $this->options[$name] ?? $default;
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->options[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->options[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new \LogicException('Cannot modify options in this way.');
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new \LogicException('Cannot modify options in this way.');
    }
}
