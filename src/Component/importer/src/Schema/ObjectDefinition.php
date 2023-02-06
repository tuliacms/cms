<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\Schema;

/**
 * @author Adam Banaszkiewicz
 */
class ObjectDefinition
{
    /**
     * @param Field[] $fields
     */
    public function __construct(
        private string $name,
        private array $fields,
        private readonly ?string $importer,
        private readonly ?string $exporter,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function hasField(string $name): bool
    {
        return isset($this->fields[$name]);
    }

    public function getField(string $name): Field
    {
        return $this->fields[$name];
    }

    public function getImporter(): ?string
    {
        return $this->importer;
    }

    public function getExporter(): ?string
    {
        return $this->exporter;
    }
}
