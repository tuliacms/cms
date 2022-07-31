<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Application\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tulia\Cms\SearchAnything\Domain\WriteModel\Service\IndexerInterface;
use Tulia\Cms\User\Domain\ReadModel\Query\UserSearchCollectorInterface;
use Tulia\Cms\User\Domain\WriteModel\Event\UserCreated;
use Tulia\Cms\User\Domain\WriteModel\Event\UserDeleted;
use Tulia\Cms\User\Domain\WriteModel\Event\UserUpdated;

/**
 * @author Adam Banaszkiewicz
 */
final class UpdateSearchAnythingWhenUserUpdates implements EventSubscriberInterface
{
    public function __construct(
        private IndexerInterface $indexer,
        private UserSearchCollectorInterface $collector
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserCreated::class => 'indexDocument',
            UserUpdated::class => 'updateDocument',
            UserDeleted::class => 'deleteDocument',
        ];
    }

    public function indexDocument(UserCreated $event): void
    {
        $this->index($event->id, $event->email, $event->name);
    }

    public function updateDocument(UserUpdated $event): void
    {
        $this->index($event->id, $event->email, $event->name);
    }

    public function deleteDocument(UserDeleted $event): void
    {
        $index = $this->indexer->index('user');

        $document = $index->document($event->id);

        $index->delete($document);
    }

    private function index(string $id, string $email, ?string $name): void
    {
        $index = $this->indexer->index('user');
        $document = $index->document($id);

        if ($name) {
            $document->setTitle($name.' - '.$email);
        } else {
            $document->setTitle($email);
        }

        $document->setLink('backend.users.edit', ['id' => $id]);

        $index->save($document);
    }
}
