<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Form\ContentType;

use Tulia\Cms\ContentBuilder\Domain\WriteModel\Exception\EmptyRoutingStrategyForRoutableContentTypeException;
use Tulia\Cms\ContentBuilder\Domain\WriteModel\Model\ContentType;
use Tulia\Cms\ContentBuilder\Domain\WriteModel\Model\Field;
use Tulia\Cms\ContentBuilder\Domain\WriteModel\Model\FieldsGroup;
use Tulia\Cms\ContentBuilder\Domain\WriteModel\Model\LayoutType;
use Tulia\Cms\ContentBuilder\Domain\WriteModel\Model\Section;
use Tulia\Cms\Shared\Domain\WriteModel\UuidGeneratorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class FormDataToModelTransformer
{
    private UuidGeneratorInterface $uuidGenerator;

    public function __construct(UuidGeneratorInterface $uuidGenerator)
    {
        $this->uuidGenerator = $uuidGenerator;
    }

    /**
     * @throws EmptyRoutingStrategyForRoutableContentTypeException
     */
    public function produceContentType(array $data, string $type, LayoutType $layout): ContentType
    {
        $nodeType = ContentType::create($this->uuidGenerator->generate(), $data['type']['code'], $type, $layout, false);
        $nodeType->setName($data['type']['name']);
        $nodeType->setIcon($data['type']['icon']);
        $nodeType->setIsHierarchical((bool) $data['type']['icon']);
        $nodeType->setRoutingStrategy($data['type']['routingStrategy'] ?? '');
        $nodeType->setIsRoutable((bool) $data['type']['isRoutable']);

        foreach ($data['layout'] as $section) {
            foreach ($section['sections'] as $group) {
                foreach ($this->collectFields($group['fields']) as $field) {
                    $nodeType->addField($field);
                }
            }
        }

        return $nodeType;
    }

    public function produceLayoutType(array $data): LayoutType
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

        foreach ($fields as $field) {
            $result[] = new Field([
                'code' => $field['code']['value'],
                'type' => $field['type']['value'],
                'name' => $field['name']['value'],
                'is_multilingual' => $field['multilingual']['value'],
                'configuration' => $this->transformConfiguration($field['configuration']),
                'constraints' => $this->transformConstraints($field['constraints']),
                'children' => $this->collectFields($field['children']),
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
