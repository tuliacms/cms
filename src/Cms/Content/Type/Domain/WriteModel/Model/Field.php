<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\WriteModel\Model;

/**
 * @author Adam Banaszkiewicz
 */
final class Field
{
    private string $code;
    private string $type;
    private string $name;
    private array $flags;
    private array $constraints;
    private array $configuration;
    private ?string $parent;

    public function __construct(
        string $code,
        string $type,
        string $name,
        array $flags = [],
        array $constraints = [],
        array $configuration = [],
        ?string $parent = null
    ) {
        $this->validateFlags($flags);
        $this->validateConstraints($constraints);
        $this->validateConfiguration($configuration);

        $this->code = $code;
        $this->type = $type;
        $this->name = $name;
        $this->flags = $flags;
        $this->constraints = $constraints;
        $this->configuration = $configuration;
        $this->parent = $parent;
    }

    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'type' => $this->type,
            'name' => $this->name,
            'flags' => $this->flags,
            'constraints' => $this->constraints,
            'configuration' => $this->configuration,
            'parent' => $this->parent,
        ];
    }

    public function getParent(): ?string
    {
        return $this->parent;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string[] $flags
     */
    private function validateFlags(array $flags): void
    {
        foreach ($flags as $flag) {
            if (!is_string($flag)) {
                throw new \InvalidArgumentException(sprintf('Flag must be a string, %s given.', gettype($flag)));
            }
        }
    }

    /**
     * @param array<int, array<string, string|array<int, array<string, string>>> $constraints
     */
    private function validateConstraints(array $constraints): void
    {
        foreach ($constraints as $constraint) {
            if (!is_array($constraint)) {
                throw new \InvalidArgumentException(sprintf('Constraint must be an array, %s given.', gettype($constraint)));
            }

            if (!isset($constraint['code'])) {
                throw new \InvalidArgumentException('Missing "code" of constraint.');
            }
            if (!isset($constraint['modificators'])) {
                throw new \InvalidArgumentException('Missing "modificators" of constraint.');
            }

            if (!is_array($constraint['modificators'])) {
                throw new \InvalidArgumentException(sprintf('Constraint\'s modificators must be an array, %s given.', gettype($constraint['modificators'])));
            }

            foreach ($constraint['modificators'] as $modificator) {
                if (!isset($modificator['code'], $modificator['value'])) {
                    throw new \InvalidArgumentException('Constraint\s modificators must contain array with "code" and "value" keys.');
                }
            }
        }
    }

    private function validateConfiguration(array $configuration): void
    {
        foreach ($configuration as $config) {
            if (!is_array($config)) {
                throw new \InvalidArgumentException(sprintf('Configuration entry must be an array, %s given.', gettype($config)));
            }

            if (!isset($config['code'], $config['value'])) {
                throw new \InvalidArgumentException('Configuration entry must contain array with "code" and "value" keys.');
            }
        }
    }
}
