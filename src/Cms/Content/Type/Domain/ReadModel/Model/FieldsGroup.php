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
        $this->fields = array_map(
            fn (array $field) => new Field($field),
            $fields
        );
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
}
