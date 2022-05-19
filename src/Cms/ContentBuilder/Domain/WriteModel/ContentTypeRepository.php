<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\WriteModel;

use Tulia\Cms\ContentBuilder\Domain\WriteModel\ContentType\Service\ContentTypeStorageInterface;
use Tulia\Cms\ContentBuilder\Domain\WriteModel\Event\ContentTypeDeleted;
use Tulia\Cms\ContentBuilder\Domain\WriteModel\Model\ContentType;
use Tulia\Cms\ContentBuilder\Domain\WriteModel\Model\Field;
use Tulia\Cms\Shared\Domain\WriteModel\UuidGeneratorInterface;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ContentTypeRepository
{
    private ContentTypeStorageInterface $storage;
    private UuidGeneratorInterface $uuidGenerator;
    private EventBusInterface $eventBus;

    public function __construct(
        ContentTypeStorageInterface $contentTypeStorage,
        UuidGeneratorInterface $uuidGenerator,
        EventBusInterface $eventBus
    ) {
        $this->storage = $contentTypeStorage;
        $this->uuidGenerator = $uuidGenerator;
        $this->eventBus = $eventBus;
    }

    public function generateId(): string
    {
        return $this->uuidGenerator->generate();
    }

    public function find(string $id): ?ContentType
    {
        $contentType = $this->storage->find($id);

        if ($contentType === []) {
            return null;
        }

        return ContentType::recreateFromArray($contentType);
    }

    public function findByCode(string $code): ?ContentType
    {
        $contentType = $this->storage->findByCode($code);

        if ($contentType === []) {
            return null;
        }

        return ContentType::recreateFromArray($contentType);
    }

    public function insert(ContentType $contentType): void
    {
        $this->storage->beginTransaction();

        try {
            $data = $this->extract($contentType);

            $this->storage->insert($data);
            $this->storage->commit();
        } catch (\Exception $exception) {
            $this->storage->rollback();
            throw $exception;
        }

        $this->eventBus->dispatchCollection($contentType->collectDomainEvents());
    }

    public function update(ContentType $contentType): void
    {
        $this->storage->beginTransaction();

        try {
            $data = $this->extract($contentType);

            $this->storage->update($data);
            $this->storage->commit();
        } catch (\Exception $exception) {
            $this->storage->rollback();
            throw $exception;
        }

        $this->eventBus->dispatchCollection($contentType->collectDomainEvents());
    }

    public function delete(ContentType $contentType): void
    {
        $this->storage->beginTransaction();

        try {
            $data = $this->extract($contentType);

            $this->storage->delete($data);
            $this->storage->commit();
        } catch (\Exception $exception) {
            $this->storage->rollback();
            throw $exception;
        }

        $this->eventBus->dispatch(ContentTypeDeleted::fromModel($contentType));
    }

    public function extract(ContentType $contentType): array
    {
        $sections = [];

        foreach ($contentType->getLayout()->getSections() as $section) {
            $fieldsGroups = [];

            foreach ($section->getFieldsGroups() as $fieldsGroup) {
                $fieldsGroups[] = [
                    'code' => $fieldsGroup->getCode(),
                    'name' => $fieldsGroup->getName(),
                    'fields' => $fieldsGroup->getFields(),
                ];
            }

            $sections[] = [
                'code' => $section->getCode(),
                'field_groups' => $fieldsGroups,
            ];
        }

        $extracted = [
            'id' => $contentType->getId(),
            'type' => $contentType->getType(),
            'controller' => $contentType->getController(),
            'code' => $contentType->getCode(),
            'name' => $contentType->getName(),
            'icon' => $contentType->getIcon(),
            'is_routable' => $contentType->isRoutable(),
            'is_hierarchical' => $contentType->isHierarchical(),
            'routing_strategy' => $contentType->getRoutingStrategy(),
            'fields' => $this->extractFields(null, $contentType->getFields()),
            'layout' => [
                'code' => $contentType->getLayout()->getCode(),
                'name' => $contentType->getLayout()->getName(),
                'sections' => $sections,
            ],
        ];

        return $extracted;
    }

    /**
     * @param Field[] $fields
     */
    private function extractFields(?string $parent, array $fields): array
    {
        $result = [];

        foreach ($fields as $field) {
            $constraints = [];

            foreach ($field->getConstraints() as $code => $info) {
                $constraints[] = [
                    'code' => $code,
                    'modificators' => $info['modificators'] ?? [],
                ];
            }

            $result[] = [[
                'code' => $field->getCode(),
                'type' => $field->getType(),
                'name' => $field->getName(),
                'is_multilingual' => $field->isMultilingual(),
                'configuration' => $field->getConfiguration(),
                'constraints' => $constraints,
                'parent' => $parent,
            ]];

            if ($field->getChildren() !== []) {
                $result[] = $this->extractFields($field->getCode(), $field->getChildren());
            }
        }

        return array_merge(...$result);
    }
}
