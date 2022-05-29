<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Infrastructure\Persistence\Domain;

use Tulia\Cms\Shared\Domain\WriteModel\UuidGeneratorInterface;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalContentTypeStorage
{
    private ConnectionInterface $connection;
    private UuidGeneratorInterface $uuidGenerator;

    public function __construct(
        ConnectionInterface $connection,
        UuidGeneratorInterface $uuidGenerator
    ) {
        $this->connection = $connection;
        $this->uuidGenerator = $uuidGenerator;
    }

    public function find(string $code): ?array
    {
        $type = $this->connection->fetchAllAssociative(
            'SELECT * FROM #__content_type WHERE code = :code LIMIT 1',
            ['code' => $code]
        );

        if ($type === []) {
            return null;
        }

        $type[0]['fields_groups'] = $this->collectFieldGroups($type[0]['code']);

        return $type[0];
    }

    public function insert(array $contentType): void
    {
        $this->connection->insert('#__content_type', [
            'code' => $contentType['code'],
            'type' => $contentType['type'],
            'name' => $contentType['name'],
            'icon' => $contentType['icon'],
            'is_routable' => $contentType['is_routable'] ? '1' : '0',
            'is_hierarchical' => $contentType['is_hierarchical'] ? '1' : '0',
            'routing_strategy' => $contentType['routing_strategy'],
        ]);

        foreach ($contentType['fields_groups'] as $groupIndex => $group) {
            $this->connection->insert('#__content_type_field_group', [
                'content_type_code' => $contentType['code'],
                'code' => $group['code'],
                'name' => $group['name'],
                'section' => $group['section'],
                'position' => $groupIndex + 1,
            ]);

            $this->insertFields($group['fields'], $group['code'], $contentType['code']);
        }
    }

    public function update(array $contentType): void
    {
        $this->connection->update('#__content_type', [
            'name' => $contentType['name'],
            'icon' => $contentType['icon'],
            'controller' => $contentType['controller'],
            'is_routable' => $contentType['is_routable'] ? '1' : '0',
            'is_hierarchical' => $contentType['is_hierarchical'] ? '1' : '0',
            'routing_strategy' => $contentType['routing_strategy'],
            'layout' => $contentType['layout']['code'],
        ], [
            'id' => $contentType['id'],
        ]);

        $this->connection->delete('#__content_type_field', ['content_type_id' => $contentType['id']]);
        $this->connection->delete('#__content_type_layout_group', ['layout_type' => $contentType['layout']['code']]);

        $this->insertFields($contentType['fields'], $contentType['id']);
        $this->insertSections($contentType['layout']['sections'], $contentType['layout']['code']);
    }

    public function delete(array $contentType): void
    {
        $this->connection->delete('#__content_type', ['code' => $contentType['code']]);
        $this->connection->delete('#__content_type_layout', ['code' => $contentType['layout']['code']]);
    }

    public function beginTransaction(): void
    {
        $this->connection->beginTransaction();
    }

    public function commit(): void
    {
        $this->connection->commit();
    }

    public function rollback(): void
    {
        $this->connection->rollBack();
    }

    private function collectFieldGroups(string $contentType): array
    {
        $groups = $this->connection->fetchAllAssociative(
            'SELECT code, name, `section` FROM #__content_type_field_group WHERE content_type_code = :content_type_code ORDER BY `position` ASC',
            ['content_type_code' => $contentType]
        );

        $result = [];

        foreach ($groups as $group) {
            $result[] = [
                'code' => $group['code'],
                'section' => $group['section'],
                'name' => $group['name'],
                'fields' => $this->collectFields($group['code'], $contentType),
            ];
        }

        return $result;
    }

    private function collectFields(string $groupCode, string $contentType): array
    {
        $fields = $this->connection->fetchAllAssociative(
            'SELECT * FROM #__content_type_field WHERE content_type_code = :content_type_code AND group_code = :group_code ORDER BY `position`',
            ['content_type_code' => $contentType, 'group_code' => $groupCode]
        );

        $result = [];

        foreach ($fields as $field) {
            $flags = [];

            if ((bool) $field['has_nonscalar_value']) {
                $flags[] = 'nonscalar_value';
            }
            if ((bool) $field['is_multilingual']) {
                $flags[] = 'multilingual';
            }

            $result[] = [
                'code' => $field['code'],
                'type' => $field['type'],
                'name' => $field['name'],
                'flags' => $flags,
                'parent' => $field['parent'],
                'configuration' => $this->collectFieldConfiguration($field['id']),
                'constraints' => $this->collectFieldConstraints($field['id']),
            ];
        }

        return $result;
        return $this->sortFieldsHierarchically($result);
    }

    private function collectFieldConfiguration(string $id): array
    {
        $configuration = [];
        $configurationSource = $this->connection->fetchAllAssociative(
            'SELECT code, value FROM #__content_type_field_configuration WHERE field_id = :field_id',
            ['field_id' => $id]
        );

        foreach ($configurationSource as $row) {
            $configuration[$row['code']] = $row['value'];
        }

        return $configuration;
    }

    private function collectFieldConstraints(string $id): array
    {
        $constraints = [];
        $constraintsSource = $this->connection->fetchAllAssociative(
            'SELECT * FROM #__content_type_field_constraint WHERE field_id = :field_id',
            ['field_id' => $id]
        );

        foreach ($constraintsSource as $constraint) {
            $modificators = [];
            $modificatorsSource = $this->connection->fetchAllAssociative(
                'SELECT code, `value` FROM #__content_type_field_constraint_modificator WHERE constraint_id = :constraint_id',
                ['constraint_id' => $constraint['id']]
            );

            foreach ($modificatorsSource as $modificator) {
                $modificators[$modificator['code']] = $modificator['value'];
            }

            $constraints[$constraint['code']] = [
                'modificators' => $modificators,
            ];
        }

        return $constraints;
    }

    private function sortFieldsHierarchically(array $fields, ?string $parent = null): array
    {
        $result = [];

        foreach ($fields as $field) {
            if ($field['parent'] === $parent) {
                $field['children'] = $this->sortFieldsHierarchically($fields, $field['code']);
                $result[] = $field;
            }
        }

        return $result;
    }

    private function insertFields(array $fields, string $groupCode, string $contentType): void
    {
        foreach ($fields as $position => $field) {
            $fieldId = $this->uuidGenerator->generate();

            $this->connection->insert('#__content_type_field', [
                'id' => $fieldId,
                'code' => $field['code'],
                'group_code' => $groupCode,
                'content_type_code' => $contentType,
                'type' => $field['type'],
                'name' => $field['name'],
                'parent' => $field['parent'],
                'is_multilingual' => in_array('multilingual', $field['flags'], true) ? '1' : '0',
                'position' => $position + 1,
            ]);

            foreach ($field['configuration'] as $config) {
                $this->connection->insert('#__content_type_field_configuration', [
                    'field_id' => $fieldId,
                    'code' => $config['code'],
                    'value' => $config['value'],
                ]);
            }

            foreach ($field['constraints'] as $constraint) {
                $constraintId = $this->uuidGenerator->generate();

                $this->connection->insert('#__content_type_field_constraint', [
                    'id' => $constraintId,
                    'field_id' => $fieldId,
                    'code' => $constraint['code'],
                ]);

                foreach ($constraint['modificators'] as $modificator) {
                    $this->connection->insert('#__content_type_field_constraint_modificator', [
                        'constraint_id' => $constraintId,
                        'modificator' => $modificator['code'],
                        'value' => $modificator['value'],
                    ]);
                }
            }
        }
    }

    private function insertSections(array $sections, string $layoutCode): void
    {
        foreach ($sections as $section) {
            $groupPosition = 0;

            foreach ($section['field_groups'] as $group) {
                $groupId = $this->uuidGenerator->generate();

                $this->connection->insert('#__content_type_layout_group', [
                    'id' => $groupId,
                    'code' => $group['code'],
                    'name' => $group['name'],
                    'section' => $section['code'],
                    'layout_type' => $layoutCode,
                    'position' => $groupPosition++,
                ]);

                $fieldPosition = 0;
                foreach ($group['fields'] as $field) {
                    $this->connection->insert('#__content_type_layout_group_field', [
                        'group_id' => $groupId,
                        'code' => $field,
                        'position' => $fieldPosition++,
                    ]);
                }
            }
        }
    }
}
