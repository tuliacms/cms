<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Infrastructure\Persistence\ContentProvider;

use Tulia\Cms\Content\Type\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\AbstractContentTypeProvider;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ContentTypeDatabaseProvider extends AbstractContentTypeProvider
{
    private ConnectionInterface $connection;
    private array $fieldGroups = [];
    private array $fields = [];
    private array $fieldConfigurations = [];
    private array $fieldConstraints = [];
    private array $fieldConstraintsModificators = [];

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function provide(): array
    {
        return array_map(
            fn (array $type) => ContentType::fromArray($type),
            $this->getTypes()
        );
    }

    private function getTypes(): array
    {
        $types = $this->connection->fetchAllAssociative('SELECT * FROM #__content_type');

        foreach ($types as $key => $contentType) {
            $types[$key]['fields_groups'] = $this->getFieldGroups($contentType['code']);
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
            if ($field['content_type_code'] === $contentType && $field['group_code'] === $groupCode) {
                $fields[] = [
                    'code' => $field['code'],
                    'type' => $field['type'],
                    'name' => $field['name'],
                    'is_multilingual' => $field['is_multilingual'],
                    'has_nonscalar_value' => $field['has_nonscalar_value'],
                    'parent' => $field['parent'],
                    'configuration' => $this->getConfiguration($field['id']),
                    'constraints' => $this->getConstraints($field['id']),
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
