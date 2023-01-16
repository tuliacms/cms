<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\ReadModel\Model;

/**
 * @author Adam Banaszkiewicz
 */
final class FieldsGroup
{
    /** @var Field[] */
    private array $fields = [];

    public function __construct(
        private string $code,
        private string $section,
        private string $name,
        private bool $active,
        array $fields,
    ) {
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
            $data['active'],
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

    public function isActive(): bool
    {
        return $this->active;
    }

    public function newField(string $code, string $type, string $name, array $options = []): Field
    {
        return $this->fields[$code] = new Field(array_merge($options, [
            'code' => $code,
            'type' => $type,
            'name' => $name,
        ]));
    }
}
