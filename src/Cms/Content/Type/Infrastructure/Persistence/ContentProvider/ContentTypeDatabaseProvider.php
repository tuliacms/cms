<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Infrastructure\Persistence\ContentProvider;

use Doctrine\DBAL\Connection;
use Tulia\Cms\Content\Type\Domain\ReadModel\ContentTypeBuilder\ContentTypeCollector;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeProviderInterface;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\FieldTypeMappingRegistry;
use Tulia\Cms\Content\Type\Domain\WriteModel\Service\Configuration;

/**
 * @author Adam Banaszkiewicz
 */
class ContentTypeDatabaseProvider implements ContentTypeProviderInterface
{
    private array $fieldGroups = [];
    private array $fields = [];
    private array $fieldConfigurations = [];
    private array $fieldConstraints = [];
    private array $fieldConstraintsModificators = [];

    public function __construct(
        private readonly Connection $connection,
        private readonly Configuration $config,
        private readonly FieldTypeMappingRegistry $fieldTypeMappingRegistry,
    ) {
    }

    public function provide(ContentTypeCollector $collector): void
    {
        foreach ($this->getTypes() as $type) {
            $typeDef = $collector->newOne($type['type'], $type['code'], $type['name']);
            $typeDef->icon = $type['icon'];
            $typeDef->isRoutable = (bool) $type['is_routable'];
            $typeDef->isHierarchical = (bool) $type['is_hierarchical'];
            $typeDef->routingStrategy = $type['routing_strategy'];
            $typeDef->controller = $type['controller'];
            $typeDef->isInternal = $type['is_internal'] ?? false;

            foreach ($type['fields_groups'] as $group) {
                $groupDef = $typeDef->fieldsGroup($group['code'], $group['name'], $group['section'], $group['active']);

                foreach ($group['fields'] as $fieldCode => $fieldArray) {
                    $groupDef->fieldFromArray($fieldCode, $fieldArray);
                }
            }
        }
    }

    private function getTypes(): array
    {
        $types = $this->connection->fetchAllAssociative('SELECT * FROM #__content_type');

        foreach ($types as $key => $contentType) {
            $types[$key]['fields_groups'] = $this->getFieldGroups($contentType['code']);
            $types[$key]['controller'] = $types[$key]['controller'] ?? $this->config->getController($contentType['type']);
        }

        return $types;
    }

    private function getFieldGroups(string $contentType): array
    {
        if ($this->fieldGroups === []) {
            $this->fieldGroups = $this->connection->fetchAllAssociative('SELECT * FROM #__content_type_field_group ORDER BY `position`');
        }

        $groups = [];

        foreach ($this->fieldGroups as $group) {
            if ($group['content_type_code'] !== $contentType) {
                continue;
            }

            $groups[] = [
                'code' => $group['code'],
                'section' => $group['section'],
                'name' => $group['name'],
                'active' => (bool) $group['active'],
                'fields' => $this->getFields($group['code'], $contentType),
            ];
        }

        return $groups;
    }

    private function getFields(string $groupCode, string $contentType): array
    {
        if ($this->fields === []) {
            $this->fields = $this->connection->fetchAllAssociative('SELECT * FROM #__content_type_field ORDER BY `position`');
        }

        $fields = [];

        foreach ($this->fields as $field) {
            if (
                $field['content_type_code'] === $contentType
                && $field['group_code'] === $groupCode
            ) {
                $fields[$field['code']] = [
                    'code' => $field['code'],
                    'type' => $field['type'],
                    'name' => $field['name'],
                    'is_multilingual' => $field['is_multilingual'],
                    'parent' => $field['parent'],
                    'configuration' => $this->getConfiguration($field['id']),
                    'constraints' => $this->getConstraints($field['id']),
                    'flags' => $this->fieldTypeMappingRegistry->getTypeFlags($field['type']),
                ];
            }
        }

        return $fields;
    }

    private function getConfiguration(string $fieldId): array
    {
        if ($this->fieldConfigurations === []) {
            $this->fieldConfigurations = $this->connection->fetchAllAssociative('SELECT * FROM #__content_type_field_configuration');
        }

        $configuration = [];

        foreach ($this->fieldConfigurations as $config) {
            if ($config['field_id'] === $fieldId) {
                $configuration[$config['code']] = $config['value'];
            }
        }

        return $configuration;
    }

    private function getConstraints(string $fieldId): array
    {
        if ($this->fieldConstraints === []) {
            $this->fieldConstraints = $this->connection->fetchAllAssociative('SELECT * FROM #__content_type_field_constraint');
        }
        if ($this->fieldConstraintsModificators === []) {
            $this->fieldConstraintsModificators = $this->connection->fetchAllAssociative('SELECT * FROM #__content_type_field_constraint_modificator');
        }

        $constraints = [];

        foreach ($this->fieldConstraints as $constraint) {
            if ($constraint['field_id'] === $fieldId) {
                $modificators = [];

                foreach ($this->fieldConstraintsModificators as $modificator) {
                    if ($modificator['constraint_id'] === $constraint['id']) {
                        $modificators[$modificator['code']] = $modificator['value'];
                    }
                }

                $constraints[] = [
                    'code' => $constraint['code'],
                    'modificators' => $modificators,
                ];
            }
        }

        return $constraints;
    }
}
