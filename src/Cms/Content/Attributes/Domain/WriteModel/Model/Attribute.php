<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Attributes\Domain\WriteModel\Model;

/**
 * @author Adam Banaszkiewicz
 */
class Attribute implements \Stringable, \Traversable, \ArrayAccess, \IteratorAggregate
{
    private string $code;
    private string $uri;
    /** @var mixed */
    private $value;
    /** @var mixed */
    private $compiledValue;
    private array $payload;
    private array $flags;
    private bool $multilingual;
    private bool $hasNonscalarValue;

    /**
     * @param mixed $value
     * @param mixed $compiledValue
     */
    public function __construct(
        string $code,
        string $uri,
        $value,
        $compiledValue,
        array $payload,
        array $flags,
        bool $multilingual,
        bool $hasNonscalarValue
    ) {
        $this->code = $code;
        $this->uri = $uri;
        $this->value = $value;
        $this->compiledValue = $compiledValue;
        $this->payload = $payload;
        $this->flags = $flags;
        $this->multilingual = $multilingual;
        $this->hasNonscalarValue = $hasNonscalarValue;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['code'],
            $data['uri'],
            $data['value'],
            $data['compiled_value'],
            $data['payload'],
            $data['flags'],
            $data['multilingual'] ? true : false,
            $data['has_nonscalar_value'] ? true : false
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
            'multilingual' => $this->multilingual,
            'has_nonscalar_value' => $this->hasNonscalarValue,
        ];
    }

    /**
     * @param mixed $value
     */
    public function withValue($value): self
    {
        $data = $this->toArray();
        $data['value'] = $value;

        return self::fromArray($data);
    }

    /**
     * @param mixed $compiledValue
     */
    public function withCompiledValue($compiledValue): self
    {
        $data = $this->toArray();
        $data['compiled_value'] = $compiledValue;

        return self::fromArray($data);
    }

    /**
     * @param array $payload
     */
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

    public function produceUriWithModificator(string $modificator): string
    {
        if ($this->uri[strlen($this->uri) - 1] === ']') {
            return substr($this->uri, 0, -1).':'.$modificator.']';
        } else {
            return $this->uri.':'.$modificator;
        }
    }

    public function produceCodeWithModificator(string $modificator): string
    {
        return $this->code.':'.$modificator;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return mixed
     */
    public function getCompiledValue()
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
        return $this->multilingual;
    }

    public function hasNonscalarValue(): bool
    {
        return $this->hasNonscalarValue;
    }

    public function getIterator(): \Iterator
    {
        if ($this->hasNonscalarValue) {
            return new \ArrayIterator($this->value);
        }

        return new \ArrayIterator([$this->value]);
    }

    public function offsetExists($offset): bool
    {
        return $this->hasNonscalarValue && isset($this->value[$offset]);
    }

    /**
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->hasNonscalarValue ? $this->value[$offset] : $this->value;
    }

    public function offsetSet($offset, $value): void
    {
        throw new \LogicException('Cannot change value of attribute.');
    }

    public function offsetUnset($offset): void
    {

    }
}
