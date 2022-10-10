<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Domain\FieldsParser;

use Tulia\Cms\ContactForm\Domain\FieldsParser\Exception\InvalidFieldNameException;

/**
 * @author Adam Banaszkiewicz
 */
class FieldsStream implements FieldsStreamInterface
{
    private array $fields = [];
    private string $source;
    private ?string $result = null;

    public function __construct(string $source)
    {
        $this->source = $source;
    }

    public function addField(string $name, array $field): void
    {
        if (! preg_match('/^[a-z0-9_]+$/i', $name)) {
            throw InvalidFieldNameException::fromName($name);
        }

        $this->fields[$name] = $field;
    }

    public function allFields(): array
    {
        return $this->fields;
    }

    public function getResult(): ?string
    {
        return $this->result;
    }

    public function setResult(?string $result): void
    {
        $this->result = $result;
    }
}
