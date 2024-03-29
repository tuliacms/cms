<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\ReadModel\Model;

/**
 * @author Adam Banaszkiewicz
 */
final class Field
{
    protected array $options;
    protected static $defaults = [
        'code' => '',
        'type' => '',
        'name' => '',
        'translation_domain' => 'content_builder.field',
        'flags' => [],
        'configuration' => [],
        'constraints' => [],
        'children' => [],
        'position' => 0,
        'is_multiple' => false,
    ];

    public function __construct(array $options) {
        $this->options = array_merge(static::$defaults, $options);

        foreach ($this->options['children'] as $key =>  $child) {
            $this->options['children'][$key] = new self($child);
        }

        \assert(\is_string($this->options['code']), 'The "code" option must be a string.');
        \assert(\is_string($this->options['type']), 'The "type" option must be a string.');
        \assert(\is_string($this->options['name']) || null === $this->options['name'], 'The "name" option must be a string or null.');
        \assert(\is_string($this->options['translation_domain']), 'The "translation_domain" option must be a string.');
        \assert(\is_array($this->options['flags']), 'The "flags" option must be an array.');
        \assert(\is_array($this->options['configuration']), 'The "configuration" option must be an array.');
        \assert(\is_array($this->options['constraints']), 'The "constraints" option must be an array.');
        \assert(\is_array($this->options['children']), 'The "children" option must be an array.');
        \assert(\is_int($this->options['position']), 'The "position" option must be an integer.');
        \assert(\is_bool($this->options['is_multiple']), 'The "is_multiple" option must be a boolean.');

        foreach ($this->options['children'] as $code => $child) {
            \assert(\is_string($code), 'The children array must be associative. Please use field code as key.');
            \assert(is_object($child) && $child instanceof Field, 'The children must be a Field instance.');
        }

        foreach ($this->options['constraints'] as $constraintName => $constraint) {
            \assert(\is_array($constraint), sprintf('Constraint "%s" of field "%s" must be an array.', $constraintName, $this->options['code']));

            if (isset($constraint['modificators'])) {
                \assert(\is_array($constraint['modificators']), sprintf('Modificators of constraint "%s" of field "%s" must be an array.', $constraintName, $this->options['code']));

                foreach ($constraint['modificators'] as $name => $value) {
                    \assert(
                        \is_string($value) || is_numeric($value) || $value === null,
                        sprintf('Value of modificator "%s" of constraint "%s" of field "%s" must be a scalar value.', $name, $constraintName, $this->options['code'])
                    );
                }
            }
        }

        foreach ($this->options['configuration'] as $configName => $configValue) {
            \assert(
                \is_scalar($configValue) || \is_array($configValue),
                sprintf('Value of configuration "%s" of field "%s" must be a scalar value.', $configName, $this->options['code'])
            );
        }
    }

    public function getCode(): string
    {
        return $this->options['code'];
    }

    public function getType(): string
    {
        return $this->options['type'];
    }

    public function isType(string $type): bool
    {
        return $this->options['type'] === $type;
    }

    public function isMultilingual(): bool
    {
        return $this->is('multilingual');
    }

    public function isMultiple(): bool
    {
        return $this->options['is_multiple'];
    }

    public function getName(): ?string
    {
        return $this->options['name'];
    }

    public function getConfiguration(): array
    {
        return $this->options['configuration'];
    }

    public function getTranslationDomain(): string
    {
        return $this->options['translation_domain'];
    }

    /**
     * @param mixed $default
     * @return mixed|null
     */
    public function getConfig(string $name, $default = null)
    {
        return $this->options['configuration'][$name] ?? $default;
    }

    public function getConstraints(): array
    {
        return $this->options['constraints'];
    }

    public function removeConstraint(string $name): void
    {
        unset($this->options['constraints'][$name]);
    }

    /**
     * @return Field[]
     */
    public function getChildren(): array
    {
        return $this->options['children'];
    }

    public function is(string $flag): bool
    {
        return \in_array($flag, $this->options['flags'], true);
    }

    /**
     * @return string[]
     */
    public function getFlags(): array
    {
        return $this->options['flags'];
    }

    public function getPosition(): int
    {
        return $this->options['position'];
    }
}
