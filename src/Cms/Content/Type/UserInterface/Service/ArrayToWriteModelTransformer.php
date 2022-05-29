<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\UserInterface\Service;

use Tulia\Cms\Content\Type\Domain\WriteModel\Exception\EmptyRoutingStrategyForRoutableContentTypeException;
use Tulia\Cms\Content\Type\Domain\WriteModel\Model\ContentType;
use Tulia\Cms\Content\Type\Domain\WriteModel\Model\Field;
use Tulia\Cms\Content\Type\Domain\WriteModel\Model\FieldsGroup;
use Tulia\Cms\Content\Type\Domain\WriteModel\Model\LayoutType;
use Tulia\Cms\Shared\Domain\WriteModel\UuidGeneratorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ArrayToWriteModelTransformer
{
    private UuidGeneratorInterface $uuidGenerator;

    public function __construct(UuidGeneratorInterface $uuidGenerator)
    {
        $this->uuidGenerator = $uuidGenerator;
    }

    /**
     * @throws EmptyRoutingStrategyForRoutableContentTypeException
     */
    public function produceContentType(string $type, array $data): ContentType
    {
        $data = $this->transformSource($data);

        return ContentType::create(
            $data['code'],
            $type,
            $data['name'],
            $data['icon'],
            $data['is_routable'] ? $data['routing_strategy'] : null,
            (bool) $data['is_hierarchical'],
            $data['fields_groups']
        );
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
                    $constraints[] = [
                        'code' => $constraint['id'],
                        'modificators' => array_map(
                            fn ($v) => ['code' => $v['id'], 'value' => $v['value']],
                            $constraint['modificators']
                        )
                    ];
                }
            }

            $flags = [];

            if ($field['multilingual']['value']) {
                $flags[] = 'multilingual';
            }

            $configuration = [];

            foreach ($field['configuration'] as $config) {
                if (isset($config['value'], $config['id'])) {
                    $configuration[] = [
                        'code' => $config['id'],
                        'value' => $config['value'],
                    ];
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















    /**
     * @throws EmptyRoutingStrategyForRoutableContentTypeException
     */
    public function fillContentType(ContentType $contentType, array $data): ContentType
    {
        $contentType->setName($data['type']['name']);
        $contentType->setIcon($data['type']['icon']);
        $contentType->setIsHierarchical((bool) $data['type']['icon']);
        $contentType->setRoutingStrategy($data['type']['routingStrategy'] ?? '');
        $contentType->setIsRoutable((bool) $data['type']['isRoutable']);

        foreach ($data['layout'] as $section) {
            foreach ($section['sections'] as $group) {
                foreach ($this->collectFields($group['fields']) as $field) {
                    $contentType->addField($field);
                }
            }
        }

        foreach ($this->transformSections($data['layout']) as $section) {
            $contentType->getLayout()->addSection($section);
        }

        return $contentType;
    }

    private function produceLayoutType(array $data): LayoutType
    {
        $layoutType = new LayoutType($data['type']['code'] . '_layout');
        $layoutType->setName($data['type']['name'] . ' Layout');

        foreach ($this->transformSections($data['layout']) as $section) {
            $layoutType->addSection($section);
        }

        return $layoutType;
    }

    /**
     * @return Field[]
     */
    private function collectFields(array $fields): array
    {
        $result = [];

        foreach ($fields as $position => $field) {
            $flags = [];

            if ($field['multilingual']['value']) {
                $flags[] = 'multilingual';
            }

            $result[$field['code']['value']] = new Field([
                'code' => $field['code']['value'],
                'type' => $field['type']['value'],
                'name' => $field['name']['value'],
                'configuration' => $this->transformConfiguration($field['configuration']),
                'constraints' => $this->transformConstraints($field['constraints']),
                'children' => $this->collectFields($field['children']),
                'position' => $position,
                'flags' => $flags,
            ]);
        }

        return $result;
    }

    private function transformConfiguration(array $configuration): array
    {
        $result = [];

        foreach ($configuration as $config) {
            $result[$config['id']] = $config['value'];
        }

        return $result;
    }

    private function transformConstraints(array $constraints): array
    {
        $result = [];

        foreach ($constraints as $constraint) {
            if (! $constraint['enabled']) {
                continue;
            }

            $modificators = [];

            foreach ($constraint['modificators'] as $modificator) {
                $modificators[$modificator['id']] = $modificator['value'];
            }

            $result[$constraint['id']]['modificators'] = $modificators;
        }

        return $result;
    }

    private function transformSections(array $sections): array
    {
        $result = [];

        foreach ($sections as $name => $data) {
            $groups = [];

            foreach ($data['sections'] as $group) {
                $fields = [];

                foreach ($group['fields'] as $field) {
                    $fields[] = $field['code']['value'];
                }

                $groups[] = new FieldsGroup(
                    $group['code'],
                    $group['name']['value'],
                    $fields
                );
            }

            $result[] = new Section($name, $groups);
        }

        return $result;
    }
}
