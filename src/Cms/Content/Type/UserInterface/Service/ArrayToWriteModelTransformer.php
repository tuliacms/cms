<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\UserInterface\Service;

use Tulia\Cms\Content\Type\Domain\WriteModel\Exception\EmptyRoutingStrategyForRoutableContentTypeException;
use Tulia\Cms\Content\Type\Domain\WriteModel\Model\ContentType;
use Tulia\Cms\Content\Type\Domain\WriteModel\Rules\CanCreateContentTypeInterface;
use Tulia\Cms\Shared\Domain\WriteModel\UuidGeneratorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ArrayToWriteModelTransformer
{
    public function __construct(
        private UuidGeneratorInterface $uuidGenerator,
        private CanCreateContentTypeInterface $canCreateContentType
    ) {
    }

    /**
     * @throws EmptyRoutingStrategyForRoutableContentTypeException
     */
    public function produceContentType(string $type, array $data): ContentType
    {
        $data = $this->transformSource($data);

        return ContentType::create(
            $this->canCreateContentType,
            $data['code'],
            $type,
            $data['name'],
            $data['icon'],
            $data['is_routable'] ? $data['routing_strategy'] : null,
            (bool) $data['is_hierarchical'],
            $data['fields_groups']
        );
    }

    /**
     * @throws EmptyRoutingStrategyForRoutableContentTypeException
     */
    public function fillContentType(ContentType $contentType, array $data): void
    {
        $data = $this->transformSource($data);

        $contentType->rename($data['name']);
        $contentType->assignIcon($data['icon']);

        if ($data['is_hierarchical']) {
            $contentType->enableHierarchical();
        } else {
            $contentType->disableHierarchical();
        }
        if ($data['is_routable']) {
            $contentType->enableRoutable($data['routing_strategy']);
        } else {
            $contentType->disableRoutable();
        }

        $source = $contentType->toArray();

        $this->updateFieldsGroups($contentType, $source['fields_groups'], $data['fields_groups']);
    }

    private function updateFieldsGroups(ContentType $contentType, array $current, array $new): void
    {
        $currentGroups = array_column($current, 'code');
        $newGroups = array_column($new, 'code');

        $toRemove = array_diff($currentGroups, $newGroups);
        $toAdd = array_diff($newGroups, $currentGroups);
        $toUpdate = array_intersect($newGroups, $currentGroups);

        foreach ($toRemove as $code) {
            $contentType->removeFieldsGroup($code);
        }

        foreach ($toAdd as $code) {
            foreach ($new as $newGroup) {
                if ($newGroup['code'] === $code) {
                    $contentType->addFieldsGroup(
                        $newGroup['code'],
                        $newGroup['name'],
                        $newGroup['section']
                    );

                    foreach ($newGroups['fields'] ?? [] as $field) {
                        $contentType->addFieldToGroup(
                            $field['code'],
                            $field['type'],
                            $field['name'],
                            $field['flags'],
                            $field['constraints'],
                            $field['configuration'],
                            $field['parent'],
                        );
                    }
                }
            }
        }

        foreach ($toUpdate as $groupCode) {
            $newFields = [];
            $currentFields = [];

            foreach ($new as $updatedGroup) {
                if ($updatedGroup['code'] === $groupCode) {
                    $contentType->renameFieldsGroup($updatedGroup['code'], $updatedGroup['name']);
                    $newFields = $updatedGroup['fields'];
                }
            }

            foreach ($current as $currentGroup) {
                if ($currentGroup['code'] === $groupCode) {
                    $currentFields = $currentGroup['fields'];
                }
            }

            $this->updateFields($contentType, $groupCode, $currentFields, $newFields);
        }

        $contentType->sortFieldsGroups($newGroups);
    }

    private function updateFields(ContentType $contentType, string $groupCode, array $current, array $new): void
    {
        $currentFields = array_column($current, 'code');
        $newFields = array_column($new, 'code');

        $toRemove = array_diff($currentFields, $newFields);
        $toAdd = array_diff($newFields, $currentFields);
        $toUpdate = array_intersect($newFields, $currentFields);

        foreach ($toUpdate as $fieldCode) {
            foreach ($new as $field) {
                if ($field['code'] === $fieldCode) {
                    $contentType->updateField(
                        $field['code'],
                        $field['name'],
                        $field['flags'],
                        $field['constraints'],
                        $field['configuration'],
                        $field['parent'],
                    );
                }
            }
        }

        foreach ($toRemove as $fieldCode) {
            $contentType->removeField($fieldCode);
        }

        foreach ($toAdd as $fieldCode) {
            foreach ($new as $field) {
                if ($field['code'] === $fieldCode) {
                    $contentType->addFieldToGroup(
                        $groupCode,
                        $field['code'],
                        $field['type'],
                        $field['name'],
                        $field['flags'],
                        $field['constraints'],
                        $field['configuration'],
                        $field['parent'],
                    );
                }
            }
        }

        $contentType->sortFields(array_column($new, 'code'));
    }

    private function transformSource(array $source): array
    {
        $groups = [];

        foreach ($source['layout'] as $section => $sourceGroups) {
            foreach ($sourceGroups['sections'] as $group) {
                $groups[] = [
                    'code' => $group['code'],
                    'name' => $group['name']['value'],
                    'section' => $section,
                    'fields' => $this->transformFields($group['fields']),
                ];
            }
        }

        return [
            'name' => $source['type']['name'],
            'code' => $source['type']['code'],
            'icon' => $source['type']['icon'],
            'is_routable' => $source['type']['isRoutable'],
            'is_hierarchical' => $source['type']['isHierarchical'],
            'routing_strategy' => $source['type']['routingStrategy'],
            'fields_groups' => $groups,
        ];
    }

    private function transformFields(array $sourceFields, ?string $parent = null): array
    {
        $fields = [];

        foreach ($sourceFields as $field) {
            $constraints = [];

            foreach ($field['constraints'] as $constraint) {
                if ($constraint['enabled']) {
                    $constraints[$constraint['code']]['modificators'] = [];

                    foreach ($constraint['modificators'] as $modificator) {
                        $constraints[$constraint['code']]['modificators'][$modificator['code']] = $modificator['value'];
                    }
                }
            }

            $flags = [];

            if ($field['multilingual']['value']) {
                $flags[] = 'multilingual';
            }

            $configuration = [];

            foreach ($field['configuration'] as $config) {
                if (isset($config['value'], $config['code'])) {
                    $configuration[$config['code']] = $config['value'];
                }
            }

            $fields[] = [
                'code' => $field['code']['value'],
                'name' => $field['name']['value'],
                'type' => $field['type']['value'],
                'flags' => $flags,
                'configuration' => $configuration,
                'constraints' => $constraints,
                'parent' => $parent,
            ];

            if ($field['children'] !== []) {
                $fields = [...$fields, ...$this->transformFields($field['children'], $field['code']['value'])];
            }
        }

        return $fields;
    }
}
