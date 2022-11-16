<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Attributes\Domain\ReadModel;

use Tulia\Cms\Content\Attributes\Domain\ReadModel\Model\AttributeValue;

/**
 * @property array $attributes
 * @method array loadAttributes()
 * @author Adam Banaszkiewicz
 */
trait LazyMagickAttributesTrait
{
    protected array $attributes = [];

    /**
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        $this->loadAttributes();

        return $this->{"$name:compiled"} ?? $this->{$name} ?? $this->attributes[$name] ?? null;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set(string $name, $value): void
    {
        $this->loadAttributes();

        if (method_exists($this, $name)) {
            $this->{$name} = $value;
        } else {
            $this->attributes[$name] = $value;
        }
    }

    public function __call(string $name , array $arguments)
    {
        $this->loadAttributes();

        if (method_exists($this, $name)) {
            return $this->{$name}(...$arguments);
        }

        return $this->attribute($name);
    }

    public function __isset(string $name): bool
    {
        $this->loadAttributes();

        return method_exists($this, $name) || isset($this->attributes[$name]) || isset($this->attributes["$name:compiled"]);
    }

    public function attribute(string $name, mixed $default = null): mixed
    {
        $this->loadAttributes();

        return $this->attributes["$name:compiled"] ?? $this->attributes[$name] ?? $default;
    }

    /**
     * @return AttributeValue[]
     */
    public function getAttributes(): array
    {
        $this->loadAttributes();

        return $this->attributes;
    }

    /**
     * @param AttributeValue[] $attributes
     */
    public function replaceAttributes(array $attributes): void
    {
        $this->loadAttributes();

        $this->attributes = $attributes;
    }

    /**
     * @param AttributeValue[] $attributes
     */
    public function mergeAttributes(array $attributes): void
    {
        $this->loadAttributes();

        $this->attributes += $attributes;
    }

    public function offsetExists(mixed $offset): bool
    {
        $this->loadAttributes();

        return array_key_exists($offset, $this->attributes) || array_key_exists("$offset:compiled", $this->attributes);
    }

    public function offsetGet(mixed $offset): ?AttributeValue
    {
        $this->loadAttributes();

        return $this->attributes["$offset:compiled"] ?? $this->attributes[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->loadAttributes();

        if (isset($this->attributes["$offset:compiled"])) {
            $this->attributes["$offset:compiled"] = $value;
        } else {
            $this->attributes[$offset] = $value;
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->loadAttributes();

        unset($this->attributes[$offset], $this->attributes["$offset:compiled"]);
    }
}
