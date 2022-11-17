<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Attributes\Domain\WriteModel\Model;

/**
 * @author Adam Banaszkiewicz
 */
class Attribute implements \Stringable
{
    protected AttributeValue $value;

    public function __construct(
        protected string $code,
        protected string $uri,
        array $value,
        protected ?string $compiledValue,
        protected array $payload,
        protected array $flags,
    ) {
        $this->value = new AttributeValue($this->processValue($value));
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
        );
    }

    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'value' => $this->value->toRaw(),
            'compiled_value' => $this->compiledValue,
            'payload' => $this->payload,
            'uri' => $this->uri,
            'flags' => $this->flags,
        ];
    }

    public function withValue(mixed $value): self
    {
        $data = $this->toArray();
        $data['value'] = is_array($value) ? $value : [$value];

        return self::fromArray($data);
    }

    public function withCompiledValue(?string $compiledValue): self
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
        return $this->compiledValue ?: $this->value->__toString();
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getValue(): AttributeValue
    {
        return $this->value;
    }

    public function getCompiledValue(): ?string
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

    public function isEmpty(): bool
    {
        return $this->value->isEmpty();
    }

    private function processValue(mixed $values): array
    {
        if ($values instanceof AttributeValue) {
            $values = $values->toRaw();
        }

        if (!$values) {
            $values = [];
        } elseif (!\is_array($values)) {
            $values = [$values];
        }

        return $values;
    }
}
