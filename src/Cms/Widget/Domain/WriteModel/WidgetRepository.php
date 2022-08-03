<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Domain\WriteModel;

use Tulia\Cms\Content\Attributes\Domain\WriteModel\AttributesRepositoryInterface;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Shared\Domain\WriteModel\UuidGeneratorInterface;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\Widget\Domain\Catalog\Registry\WidgetRegistryInterface;
use Tulia\Cms\Widget\Domain\WriteModel\Event\WidgetDeleted;
use Tulia\Cms\Widget\Domain\WriteModel\Event\WidgetUpdated;
use Tulia\Cms\Widget\Domain\WriteModel\Exception\WidgetNotFoundException;
use Tulia\Cms\Widget\Domain\WriteModel\Model\Widget;

/**
 * @author Adam Banaszkiewicz
 */
class WidgetRepository
{
    private WidgetWriteStorageInterface $storage;
    private WidgetRegistryInterface $widgetRegistry;
    private UuidGeneratorInterface $uuidGenerator;
    private AttributesRepositoryInterface $attributeRepository;
    private EventBusInterface $eventBus;
    private ContentTypeRegistryInterface $contentTypeRegistry;

    public function __construct(
        WidgetWriteStorageInterface $storage,
        WidgetRegistryInterface $widgetRegistry,
        UuidGeneratorInterface $uuidGenerator,
        AttributesRepositoryInterface $attributeRepository,
        EventBusInterface $eventBus,
        ContentTypeRegistryInterface $contentTypeRegistry
    ) {
        $this->storage = $storage;
        $this->widgetRegistry = $widgetRegistry;
        $this->uuidGenerator = $uuidGenerator;
        $this->attributeRepository = $attributeRepository;
        $this->eventBus = $eventBus;
        $this->contentTypeRegistry = $contentTypeRegistry;
    }

    public function createNew(string $widgetType): Widget
    {
        return Widget::createNew(
            $this->uuidGenerator->generate(),
            $widgetType,
            'en_US'
        );
    }

    /**
     * @throws WidgetNotFoundException
     */
    public function find(string $id): Widget
    {
        $data = $this->storage->find(
            $id,
            'en_US',
            'en_US',
        );

        if ($data === []) {
            throw new WidgetNotFoundException(sprintf('Widget %s not found.', $id));
        }

        $contentType = $this->contentTypeRegistry->get($data['content_type']);
        $data['attributes'] = $this->attributeRepository->findAll('widget', $id, $contentType->buildAttributesMapping());

        return Widget::buildFromArray($data);
    }

    public function insert(Widget $widget): void
    {
        $this->storage->beginTransaction();

        try {
            $this->storage->insert($widget->toArray(), $this->currentWebsite->getDefaultLocale()->getCode());
            $this->attributeRepository->persist(
                'widget',
                $widget->getId()->getValue(),
                $widget->getAttributes()
            );
            $this->storage->commit();
        } catch (\Exception $exception) {
            $this->storage->rollback();
            throw $exception;
        }

        $this->eventBus->dispatchCollection($widget->collectDomainEvents());
    }

    public function update(Widget $widget): void
    {
        $this->storage->beginTransaction();

        try {
            $this->storage->update($widget->toArray(), $this->currentWebsite->getDefaultLocale()->getCode());
            $this->attributeRepository->persist(
                'widget',
                $widget->getId()->getValue(),
                $widget->getAttributes()
            );
            $this->storage->commit();
        } catch (\Exception $exception) {
            $this->storage->rollback();
            throw $exception;
        }

        $this->eventBus->dispatchCollection(array_merge($widget->collectDomainEvents(), [WidgetUpdated::fromWidget($widget)]));
    }

    public function delete(Widget $widget): void
    {
        $this->storage->beginTransaction();

        try {
            $this->storage->delete($widget->toArray());
            $this->attributeRepository->delete('widget', $widget->getId()->getValue());
            $this->storage->commit();
        } catch (\Exception $exception) {
            $this->storage->rollback();
            throw $exception;
        }

        $this->eventBus->dispatch(WidgetDeleted::fromWidget($widget));
    }

    private function transformToContentTypeCode(string $widgetTypeCode): string
    {
        return 'widget_' . str_replace('.', '_', $widgetTypeCode);
    }
}
