<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Attributes\Domain\WriteModel\Model;

/**
 * @author Adam Banaszkiewicz
 */
class Attribute implements \Stringable, \Traversable, \ArrayAccess, \IteratorAggregate
{
    protected string $code;
    protected string $uri;
    protected mixed $value;
    protected mixed $compiledValue;
    protected array $payload;
    protected array $flags;

    public function __construct(
        string $code,
        string $uri,
        mixed $value,
        mixed $compiledValue,
        array $payload,
        array $flags
    ) {
        $this->code = $code;
        $this->uri = $uri;
        $this->value = $value;
        $this->compiledValue = $compiledValue;
        $this->payload = $payload;
        $this->flags = $flags;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['code'],
            $data['uri'],
            $data['value'],
            $data['compiled_value'],
            $data['payload'],
            $data['flags']
        );
    }

    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'value' => $this->value,
            'compiled_value' => $this->compiledValue,
            'payload' => $this->payload,
            'uri' => $this->uri,
            'flags' => $this->flags,
        ];
    }

    public function withValue(mixed $value): self
    {
        $data = $this->toArray();
        $data['value'] = $value;

        return self::fromArray($data);
    }

    public function withCompiledValue(mixed $compiledValue): self
    {
        $data = $this->toArray();
        $data['compiled_value'] = $compiledValue;

        return self::fromArray($data);
    }

    public function withPayload(array $payload): self
    {
        $data = $this->toArray();
        $data['payload'] = $payload;

        return self::fromArray($data);
    }

    public function equals(self $attribute): bool
    {
        return $this->value === $attribute->value
            && $this->compiledValue === $attribute->compiledValue
            && $this->payload === $attribute->payload;
    }

    public function __toString(): string
    {
        return (string) $this->compiledValue;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function getCompiledValue(): mixed
    {
        return $this->compiledValue;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * @return string[]
     */
    public function getFlags(): array
    {
        return $this->flags;
    }

    public function is(string $flag): bool
    {
        return \in_array($flag, $this->flags, true);
    }

    public function isCompilable(): bool
    {
        return $this->is('compilable');
    }

    public function isMultilingual(): bool
    {
        return $this->is('multilingual');
    }

    public function isNonscalarValue(): bool
    {
        return $this->is('nonscalar_value');
    }

    public function isEmpty(): bool
    {
        return empty($this->value) && empty($this->compiledValue) && empty($this->payload);
    }

    public function getIterator(): \Iterator
    {
        if ($this->isNonscalarValue()) {
            return new \ArrayIterator($this->value);
        }

        return new \ArrayIterator([$this->value]);
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->isNonscalarValue() && isset($this->value[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->isNonscalarValue() ? $this->value[$offset] : $this->value;
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
