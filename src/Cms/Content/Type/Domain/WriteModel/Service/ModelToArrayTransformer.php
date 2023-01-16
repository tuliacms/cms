<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\WriteModel\Service;

use Tulia\Cms\Content\Type\Domain\WriteModel\Model\ContentType;

/**
 * @author Adam Banaszkiewicz
 */
class ModelToArrayTransformer
{
    public function transform(ContentType $contentType): array
    {
        $type = $contentType->toArray();

        $data = [
            'type' => [
                'code' => $type['code'],
                'name' => $type['name'],
                'icon' => $type['icon'],
                'isRoutable' => $type['is_routable'],
                'isHierarchical' => $type['is_hierarchical'],
                'routingStrategy' => $type['routing_strategy'],
            ],
            'layout' => [
                'sidebar' => [
                    'sections' => $this->transformGroups($type, 'sidebar'),
                ],
                'main' => [
                    'sections' => $this->transformGroups($type, 'main'),
                ],
            ],
        ];

        return $data;
    }

    private function transformGroups(array $contentType, string $sectionName): array
    {
        $groups = [];

        foreach ($contentType['fields_groups'] as $group) {
            if ($group['section'] !== $sectionName) {
                continue;
            }

            $groups[] = [
                'code' => $group['code'],
                'active' => $group['active'],
                'name' => [
                    'value' => $group['name'],
                    'valid' => true,
                    'message' => null,
                ],
                'fields' => $this->transformFields($group['fields']),
            ];
        }

        return $groups;
    }

    /**
     * @param array<int, mixed> $fields
     */
    private function transformFields(array $fields, ?string $parent = null): array
    {
        $result = [];

        foreach ($fields as $field) {
            if ($field['parent'] !== $parent) {
                continue;
            }

            $result[] = [
                'metadata' => [
                    'has_errors' => false,
                ],
                'code' => [
                    'value' => $field['code'],
                    'valid' => true,
                    'message' => null,
                ],
                'name' => [
                    'value' => $field['name'],
                    'valid' => true,
                    'message' => null,
                ],
                'multilingual' => [
                    'value' => \in_array('multilingual', $field['flags'], true),
                    'valid' => true,
                    'message' => null,
                ],
                'type' => [
                    'value' => $field['type'],
                    'valid' => true,
                    'message' => null,
                ],
                'configuration' => $this->transformFieldConfiguration($field['configuration']),
                'constraints' => $this->transformFieldConstraints($field['constraints']),
                'children' => $this->transformFields($fields, $field['code']),
            ];
        }

        return $result;
    }

    private function transformFieldConfiguration(array $configuration): array
    {
        $result = [];

        foreach ($configuration as $name => $value) {
            $result[] = [
                'code' => $name,
                'value' => $value,
                'valid' => true,
                'message' => null,
            ];
        }

        return $result;
    }

    private function transformFieldConstraints(array $constraints): array
    {
        $result = [];

        foreach ($constraints as $name => $modificatorsSource) {
            $modificators = [];

            foreach ($modificatorsSource['modificators'] as $code => $value) {
                $modificators[] = [
                    'code' => $code,
                    'value' => $value,
                    'valid' => true,
                    'message' => null,
                ];
            }

            $result[] = [
                'code' => $name,
                'enabled' => true,
                'valid' => true,
                'message' => null,
                'modificators' => $modificators,
            ];
        }

        return $result;
    }
}
