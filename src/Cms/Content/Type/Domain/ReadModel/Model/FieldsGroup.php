<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\ReadModel\Model;

/**
 * @author Adam Banaszkiewicz
 */
final class FieldsGroup
{
    private string $code;
    private string $section;
    private string $name;
    /** @var Field[] */
    private array $fields = [];

    public function __construct(string $code, string $section, string $name, array $fields)
    {
        $this->code = $code;
        $this->section = $section;
        $this->name = $name;

        foreach ($fields as $field) {
            $this->fields[$field['code']] = new Field($field);
        }
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['code'],
            $data['section'],
            $data['name'],
            $data['fields']
        );
    }

    /**
     * @return Field[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    public function getFieldsCodes(): array
    {
        return array_map(
            static fn($v) => $v->getCode(),
            $this->fields
        );
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getSection(): string
    {
        return $this->section;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
