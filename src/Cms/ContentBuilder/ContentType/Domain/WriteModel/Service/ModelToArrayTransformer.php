<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\ContentType\Domain\WriteModel\Service;

use Tulia\Cms\ContentBuilder\ContentType\Domain\WriteModel\Model\ContentType;
use Tulia\Cms\ContentBuilder\ContentType\Domain\WriteModel\Model\Field;
use Tulia\Cms\ContentBuilder\ContentType\Domain\WriteModel\Model\LayoutType;

/**
 * @author Adam Banaszkiewicz
 */
class ModelToArrayTransformer
{
    public function transform(ContentType $contentType, LayoutType $layoutType): array
    {
        $data = [
            'type' => [
                'code' => $contentType->getCode(),
                'name' => $contentType->getName(),
                'icon' => $contentType->getIcon(),
                'isRoutable' => $contentType->isRoutable(),
                'isHierarchical' => $contentType->isHierarchical(),
                'routingStrategy' => $contentType->getRoutingStrategy(),
            ],
            'layout' => [
                'sidebar' => [
                    'sections' => $this->transformGroups($contentType, $layoutType, 'sidebar'),
                ],
                'main' => [
                    'sections' => $this->transformGroups($contentType, $layoutType, 'main'),
                ],
            ],
        ];

        return $data;
    }

    private function transformGroups(ContentType $contentType, LayoutType $layoutType, string $sectionName): array
    {
        $groups = [];

        foreach ($layoutType->getSections() as $section) {
            if ($section->getCode() !== $sectionName) {
                continue;
            }

            foreach ($section->getFieldsGroups() as $group) {
                $groups[] = [
                    'code' => $group->getCode(),
                    'name' => [
                        'value' => $group->getName(),
                        'valid' => true,
                        'message' => null,
                    ],
                    'fields' => $this->transformFields($group->getFields(), $contentType, $contentType->getFields()),
                ];
            }
        }

        return $groups;
    }

    /**
     * @param string[] $allowedFields
     * @param Field[] $fields
     */
    private function transformFields(array $allowedFields, ContentType $contentType, array $fields): array
    {
        $result = [];

        foreach ($fields as $field) {
            // Only allowed fields (from defined group) can be returned here.
            if (!in_array($field->getCode(), $allowedFields, true)) {
                continue;
            }

            $allowedSubfields = array_map(function ($field) {
                return $field->getCode();
            }, $field->getChildren());

            $result[] = [
                'metadata' => [
                    'has_errors' => false,
                ],
                'code' => [
                    'value' => $field->getCode(),
                    'valid' => true,
                    'message' => null,
                ],
                'name' => [
                    'value' => $field->getName(),
                    'valid' => true,
                    'message' => null,
                ],
                'multilingual' => [
                    'value' => $field->isMultilingual(),
                    'valid' => true,
                    'message' => null,
                ],
                'type' => [
                    'value' => $field->getType(),
                    'valid' => true,
                    'message' => null,
                ],
                'configuration' => $this->transformFieldConfiguration($field->getConfiguration()),
                'constraints' => $this->transformFieldConstraints($field->getConstraints()),
                'children' => $this->transformFields($allowedSubfields, $contentType, $field->getChildren()),
            ];
        }

        return $result;
    }

    private function transformFieldConfiguration(array $configuration): array
    {
        $result = [];

        foreach ($configuration as $name => $value) {
            $result[] = [
                'id' => $name,
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

            foreach ($modificatorsSource['modificators'] as $id => $value) {
                $modificators[] = [
                    'id' => $id,
                    'value' => $value,
                    'valid' => true,
                    'message' => null,
                ];
            }

            $result[] = [
                'id' => $name,
                'enabled' => true,
                'valid' => true,
                'message' => null,
                'modificators' => $modificators,
            ];
        }

        return $result;
    }
}