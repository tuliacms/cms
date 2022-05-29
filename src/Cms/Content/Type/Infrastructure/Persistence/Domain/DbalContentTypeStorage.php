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

    public function find(string $id): ?array
    {
        $type = $this->connection->fetchAllAssociative(
            'SELECT * FROM #__content_type WHERE id = :id LIMIT 1',
            ['id' => $id]
        );

        if ($type === []) {
            return null;
        }

        $type[0]['fields_groups'] = $this->collectFieldGroups($type[0]['id'], $type[0]['layout']);

        dump($type);exit;

        return $this->collectDetailsForType($type[0]);
    }

    public function findByCode(string $code): ?array
    {
        $type = $this->connection->fetchAllAssociative(
            'SELECT * FROM #__content_type WHERE code = :code LIMIT 1',
            ['code' => $code]
        );

        if ($type === []) {
            return null;
        }

        return $this->collectDetailsForType($type[0]);
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

    private function collectDetailsForType(array $type): array
    {
        $type['fields'] = $this->collectFields($type['id']);
        $type['layout'] = $this->collectLayoutDefault($type['layout']);

        return $type;
    }

    private function collectFieldGroups(string $contentTypeId, string $layoutCode): array
    {
        $groups = $this->connection->fetchAllAssociative(
            'SELECT id, code, name, `section` FROM #__content_type_layout_group WHERE layout_type = :layout_type ORDER BY `position` ASC',
            ['layout_type' => $layoutCode]
        );

        foreach ($groups as $key => $group) {
            //$group[$key]['fields'] = $this->collectFields();
        }

        return $groups;
    }

    private function collectFields(string $contentTypeId): array
    {
        $fields = $this->connection->fetchAllAssociative(
            'SELECT * FROM #__content_type_field WHERE content_type_id = :content_type_id ORDER BY `position`',
            ['content_type_id' => $contentTypeId]
        );

        foreach ($fields as $key => $field) {
            $fields[$key]['configuration'] = $this->getFieldConfiguration($field['id']);
            $fields[$key]['constraints'] = $this->getFieldConstraints($field['id']);
            $fields[$key]['flags'] = [];

            if ((bool) $fields[$key]['has_nonscalar_value']) {
                $fields[$key]['flags'][] = 'nonscalar_value';
            }
            if ((bool) $fields[$key]['is_multilingual']) {
                $fields[$key]['flags'][] = 'multilingual';
            }

            unset($fields[$key]['has_nonscalar_value'], $fields[$key]['is_multilingual']);
        }

        return $this->sortFieldsHierarchically(null, $fields);
    }

    private function getFieldConfiguration(string $id): array
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

    private function getFieldConstraints(string $id): array
    {
        $constraints = [];
        $constraintsSource = $this->connection->fetchAllAssociative(
            'SELECT * FROM #__content_type_field_constraint WHERE field_id = :field_id',
            ['field_id' => $id]
        );

        foreach ($constraintsSource as $constraint) {
            $modificators = [];
            $modificatorsSource = $this->connection->fetchAllAssociative(
                'SELECT modificator, value FROM #__content_type_field_constraint_modificator WHERE constraint_id = :constraint_id',
                ['constraint_id' => $constraint['id']]
            );

            foreach ($modificatorsSource as $modificator) {
                $modificators[$modificator['modificator']] = $modificator['value'];
            }

            $constraints[$constraint['code']] = [
                'modificators' => $modificators,
            ];
        }

        return $constraints;
    }

    private function collectLayoutDefault(string $code): array
    {
        $layout = $this->connection->fetchAllAssociative(
            'SELECT * FROM #__content_type_layout WHERE code = :code',
            ['code' => $code]
        );

        if ($layout === []) {
            // @todo What to do when Layout in any case not exists in storage? Throw domain exception?
        }

        $layout = $layout[0];

        $sourceGroups = $this->connection->fetchAllAssociative(
            'SELECT * FROM #__content_type_layout_group WHERE layout_type = :layout_type ORDER BY `position` ASC',
            ['layout_type' => $code]
        );

        foreach ($sourceGroups as $group) {
            $groupFields = $this->connection->fetchFirstColumn(
                'SELECT code FROM #__content_type_layout_group_field WHERE group_id = :group_id ORDER BY `position` ASC',
                ['group_id' => $group['id']]
            );

            $group['fields'] = $groupFields;
            $layout['sections'][$group['section']]['groups'][$group['code']] = $group;
        }

        return $layout;
    }

    private function sortFieldsHierarchically(?string $parent, array $fields): array
    {
        $result = [];

        foreach ($fields as $field) {
            if ($field['parent'] === $parent) {
                $field['children'] = $this->sortFieldsHierarchically($field['code'], $fields);
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
