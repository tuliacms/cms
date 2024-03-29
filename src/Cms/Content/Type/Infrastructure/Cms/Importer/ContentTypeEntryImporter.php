<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Infrastructure\Cms\Importer;

use Tulia\Cms\Content\Type\Domain\ReadModel\Service\FieldTypeMappingRegistry;
use Tulia\Cms\Content\Type\Domain\WriteModel\ContentTypeRepositoryInterface;
use Tulia\Cms\Content\Type\Domain\WriteModel\Model\ContentType;
use Tulia\Cms\Content\Type\Domain\WriteModel\Service\Configuration;
use Tulia\Component\Importer\ObjectImporter\ObjectImporterInterface;
use Tulia\Component\Importer\Structure\ObjectData;

/**
 * @author Adam Banaszkiewicz
 */
class ContentTypeEntryImporter implements ObjectImporterInterface
{
    private ContentTypeRepositoryInterface $repository;
    private FieldTypeMappingRegistry $fieldTypeMappingRegistry;
    private Configuration $config;

    public function __construct(
        ContentTypeRepositoryInterface $repository,
        FieldTypeMappingRegistry $fieldTypeMappingRegistry,
        Configuration $config
    ) {
        $this->repository = $repository;
        $this->fieldTypeMappingRegistry = $fieldTypeMappingRegistry;
        $this->config = $config;
    }

    public function import(ObjectData $objectData): ?string
    {
        $currentModel = $this->repository->find($objectData['code']);

        $data = $objectData->toArray();

        $contentType = $this->transformArrayToModel($data);

        if ($currentModel) {
            $this->repository->update($contentType);
        } else {
            $this->repository->insert($contentType);
        }

        return null;
    }

    private function transformArrayToModel(array $type): ContentType
    {
        $layout = [
            'code' => $type['code'] . '_layout',
            'name' => $type['name'] . ' Layout',
            'sections' => [],
        ];
        $fieldsAggs = [];

        foreach ($type['field_groups'] as $group) {
            $layout['sections'][$group['section']]['groups'][] = [
                'code' => $group['code'],
                'name' => $group['name'],
                'fields' => array_map(function (array $field) {
                    return $field['code'];
                }, $group['fields']),
            ];

            $fieldsAggs = $group['fields'] + $fieldsAggs;
        }

        $transformed = [
            'id' => $type['id'],
            'code' => $type['code'],
            'type' => $type['type'],
            'name' => $type['name'],
            'icon' => $type['icon'] ?? '',
            'is_routable' => $type['is_routable'] ?? false,
            'is_hierarchical' => $type['is_hierarchical'] ?? false,
            'routing_strategy' => $type['routing_strategy'] ?? '',
            'fields' => $fieldsAggs,
            'layout' => $layout
        ];

        return ContentType::recreateFromArray($transformed);
    }
}
