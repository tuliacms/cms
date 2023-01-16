<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\ReadModel\ContentTypeBuilder\Definition;

/**
 * @author Adam Banaszkiewicz
 */
final class FieldsGroupDefinition
{
    private array $fields = [];

    public function __construct(
        public readonly string $code,
        public readonly string $name,
        public readonly string $section = 'main',
        public readonly bool $active = false,
    ) {
    }

    public function fieldFromArray(string $code, array $data): FieldDefinition
    {
        return $this->fields[$code] = FieldDefinition::fromArray($data);
    }

    public function field(string $code, string $type, string $name, array $options = []): FieldDefinition
    {
        return $this->fields[$code] = FieldDefinition::fromArray(array_merge([
            'code' => $code,
            'type' => $type,
            'name' => $name,
        ], $options));
    }

    /**
     * @return FieldDefinition[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }
}
