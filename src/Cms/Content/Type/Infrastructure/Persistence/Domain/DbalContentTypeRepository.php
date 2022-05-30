<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Infrastructure\Persistence\Domain;

use Tulia\Cms\Content\Type\Domain\WriteModel\ContentTypeRepositoryInterface;
use Tulia\Cms\Content\Type\Domain\WriteModel\Event\ContentTypeDeleted;
use Tulia\Cms\Content\Type\Domain\WriteModel\Model\ContentType;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalContentTypeRepository implements ContentTypeRepositoryInterface
{
    private DbalContentTypeStorage $storage;
    private EventBusInterface $eventBus;

    public function __construct(
        DbalContentTypeStorage $contentTypeStorage,
        EventBusInterface $eventBus
    ) {
        $this->storage = $contentTypeStorage;
        $this->eventBus = $eventBus;
    }

    public function find(string $id): ?ContentType
    {
        $contentType = $this->storage->find($id);

        if (empty($contentType)) {
            return null;
        }

        return ContentType::fromArray($contentType);
    }

    public function insert(ContentType $contentType): void
    {
        $this->storage->beginTransaction();

        try {
            $this->storage->insert($contentType->toArray());
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
            $this->storage->update($contentType->toArray());
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
            $this->storage->delete($contentType->toArray());
            $this->storage->commit();
        } catch (\Exception $exception) {
            $this->storage->rollback();
            throw $exception;
        }

        $this->eventBus->dispatch(new ContentTypeDeleted($contentType->getCode()));
    }
}
