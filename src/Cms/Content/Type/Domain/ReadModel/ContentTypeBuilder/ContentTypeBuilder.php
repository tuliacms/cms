<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\ReadModel\ContentTypeBuilder;

use Tulia\Cms\Content\Type\Domain\ReadModel\ContentTypeBuilder\Definition\ContentTypeDefinition;
use Tulia\Cms\Content\Type\Domain\ReadModel\ContentTypeBuilder\Definition\FieldDefinition;
use Tulia\Cms\Content\Type\Domain\ReadModel\ContentTypeBuilder\Definition\FieldsGroupDefinition;
use Tulia\Cms\Content\Type\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\FieldTypeMappingRegistry;

/**
 * @author Adam Banaszkiewicz
 */
final class ContentTypeBuilder
{
    public function __construct(
        private readonly FieldTypeMappingRegistry $mappingRegistry,
    ) {
    }

    public function build(ContentTypeDefinition $definition): ContentType
    {
        return ContentType::fromArray([
            'type' => $definition->type,
            'code' => $definition->code,
            'name' => $definition->name,
            'icon' => $definition->icon,
            'is_routable' => $definition->isRoutable,
            'is_hierarchical' => $definition->isHierarchical,
            'routing_strategy' => $definition->routingStrategy,
            'controller' => $definition->controller,
            'fields_groups' => $this->buildFieldsGroups($definition->getFieldsGroups()),
        ]);
    }

    /**
     * @param FieldsGroupDefinition[] $groups
     */
    private function buildFieldsGroups(array $groups): array
    {
        $collection = [];

        foreach ($groups as $group) {
            $collection[$group->code] = [
                'code' => $group->code,
                'section' => $group->section,
                'name' => $group->name,
                'active' => $group->active,
                'fields' => $this->buildFields($group->getFields()),
            ];
        }

        return $collection;
    }

    /**
     * @param FieldDefinition[] $fields
     */
    private function buildFields(array $fields, ?string $parent = null): array
    {
        $collection = [];

        foreach ($fields as $field) {
            if ($field->parent !== $parent) {
                continue;
            }

            $info = $this->mappingRegistry->get($field->type);

            $collection[$field->code] = [
                'code' => $field->code,
                'type' => $field->type,
                'name' => $field->name,
                'translation_domain' => $field->translationDomain,
                'flags' => $field->flags,
                'configuration' => $field->configuration,
                'constraints' => $field->constraints,
                'children' => $this->buildFields($fields, $field->code),
                'position' => $field->position,
                'is_multiple' => $info['is_multiple'],
            ];
        }

        return $collection;
    }
}
