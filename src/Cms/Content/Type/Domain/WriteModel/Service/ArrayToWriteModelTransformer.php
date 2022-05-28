<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\WriteModel\Service;

use Tulia\Cms\Content\Type\Domain\WriteModel\Exception\EmptyRoutingStrategyForRoutableContentTypeException;
use Tulia\Cms\Content\Type\Domain\WriteModel\Model\ContentType;
use Tulia\Cms\Content\Type\Domain\WriteModel\Model\Field;
use Tulia\Cms\Content\Type\Domain\WriteModel\Model\FieldsGroup;
use Tulia\Cms\Content\Type\Domain\WriteModel\Model\LayoutType;
use Tulia\Cms\Content\Type\Domain\WriteModel\Model\Section;
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

    /**
     * @throws EmptyRoutingStrategyForRoutableContentTypeException
     */
    public function produceContentType(string $type, array $data): ContentType
    {
        $contentType = ContentType::create(
            $this->uuidGenerator->generate(),
            $data['type']['code'],
            $type,
            $this->produceLayoutType($data),
            false
        );
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
