<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\ReadModel\ContentTypeBuilder\Definition;

/**
 * @author Adam Banaszkiewicz
 */
final class ContentTypeDefinition
{
    public ?string $icon = null;
    public bool $isRoutable = false;
    public bool $isHierarchical = false;
    public ?string $routingStrategy = null;
    public ?string $controller = null;
    public bool $isInternal = false;

    private array $groups = [];

    public function __construct(
        public readonly string $type,
        public readonly string $code,
        public readonly string $name,
    ) {
    }

    public function fieldsGroup(string $code, string $name, string $section = 'main', bool $active = false): FieldsGroupDefinition
    {
        return $this->groups[$code] = new FieldsGroupDefinition($code, $name, $section, $active);
    }

    /**
     * @return FieldsGroupDefinition[]
     */
    public function getFieldsGroups(): array
    {
        return $this->groups;
    }
}
