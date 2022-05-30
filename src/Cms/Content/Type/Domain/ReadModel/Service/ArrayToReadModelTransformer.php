<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\ReadModel\Service;

use Tulia\Cms\Content\Type\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\Content\Type\Domain\ReadModel\Model\Field;
use Tulia\Cms\Content\Type\Domain\ReadModel\Model\FieldsGroup;
use Tulia\Cms\Content\Type\Domain\ReadModel\Model\LayoutType;
use Tulia\Cms\Content\Type\Domain\ReadModel\Model\Section;
use Tulia\Cms\Content\Type\Domain\WriteModel\Service\Configuration;

/**
 * @author Adam Banaszkiewicz
 */
class ArrayToReadModelTransformer
{
    private FieldTypeMappingRegistry $fieldTypeMappingRegistry;
    private Configuration $config;

    public function __construct(
        FieldTypeMappingRegistry $fieldTypeMappingRegistry,
        Configuration $config
    ) {
        $this->fieldTypeMappingRegistry = $fieldTypeMappingRegistry;
        $this->config = $config;
    }

    /**
     * Array structure of $type argument:
     * [
     *     id: null|string,
     *     code: string,
     *     type: string,
     *     name: string,
     *     icon: null|string,
     *     controller: null|string,
     *     is_routable: null|bool,
     *     is_hierarchical: null|bool,
     *     routing_strategy: null|string,
     *     layout: [
     *         sections: [
     *             {section_code}: [...],
     *             {section_code}: [
     *                 groups: [
     *                     {group_code}: [...],
     *                     {group_code}: [
     *                         name: string,
     *                         active: bool,
     *                         fields: [
     *                             {field_code}: [...],
     *                             {field_code}: [
     *                                 type: string,
     *                                 name: string,
     *                                 translation_domain: string,
     *                                 is_multilingual: null|bool,
     *                                 configuration: [
     *                                     {code}: {value} mixed,
     *                                     ...
     *                                 ],
     *                                 constraints: [
     *                                     {constraint_name}: [
     *                                         modificators: [
     *                                             {code}: {value} mixed,
     *                                             ...
     *                                         ]
     *                                     ]
     *                                 ],
     *                                 children: [...]
     *                             ]
     *                         ]
     *                     ]
     *                 ]
     *             ]
     *         ]
     *     ]
     * ]
     */
    public function transform(array $type): ContentType
    {
        return $this->buildContentType($type);
    }

    protected function buildContentType(array $type): ContentType
    {
        $layoutType = $this->buildLayoutType($type);
        $contentType = new ContentType($type['id'] ?? null, $type['code'], $type['type'], $layoutType, (bool) ($type['internal'] ?? false));
        $contentType->setController($type['controller'] ?? $this->config->getController($type['type']));
        $contentType->setIcon($type['icon'] ?? 'fa fa-box');
        $contentType->setName($type['name']);
        $contentType->setIsHierarchical((bool) $type['is_hierarchical']);
        $contentType->setRoutingStrategy($type['routing_strategy']);

        foreach ($type['layout']['sections'] as $sectionCode => $section) {
            foreach ($section['groups'] as $groupCode => $group) {
                foreach ($this->buildFields($group['fields']) as $field) {
                    $contentType->addField($field);
                }
            }
        }

        /**
         * Those following type needs fields to be set, so first we add fields,
         * and then those type.
         */
        $contentType->setIsRoutable((bool) $type['is_routable']);

        return $contentType;
    }

    protected function buildFields(array $fields): array
    {
        $result = [];

        foreach ($fields as $code => $field) {
            $flags = $this->fieldTypeMappingRegistry->getTypeFlags($field['type']);

            if ($field['is_multilingual'] ?? false) {
                $flags[] = 'multilingual';
            }

            $result[$code] = new Field([
                'code' => $code,
                'type' => $field['type'],
                'name' => (string) $field['name'],
                'taxonomy' => $field['taxonomy'] ?? null,
                'translation_domain' => $field['translation_domain'] ?? 'content_builder.field',
                'configuration' => $field['configuration'] ?? [],
                'constraints' => $field['constraints'] ?? [],
                'children' => $this->buildFields($field['children'] ?? []),
                'flags' => $flags,
            ]);
        }

        return $result;
    }

    protected function buildLayoutType(array $type): LayoutType
    {
        $layoutType = new LayoutType($type['code'] . '_layout');
        $layoutType->setName($type['name'] . ' Layout');

        foreach ($type['layout']['sections'] as $sectionCode => $section) {
            $sectionObject = new Section($sectionCode);

            foreach ($section['groups'] as $groupCode => $group) {
                $sectionObject->addFieldsGroup(new FieldsGroup($groupCode, $group['name'], array_keys($group['fields']), (bool) ($group['active'] ?? false)));
            }

            $layoutType->addSection($sectionObject);
        }

        return $layoutType;
    }
}