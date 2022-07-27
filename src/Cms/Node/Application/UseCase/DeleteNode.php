<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Application\UseCase;

use Tulia\Cms\Node\Domain\WriteModel\Rules\CanDeleteNode\CanDeleteNodeInterface;
use Tulia\Cms\Node\Domain\WriteModel\Service\NodeRepositoryInterface;
use Tulia\Cms\Shared\Domain\WriteModel\ActionsChain\AggregateActionsChainInterface;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class DeleteNode
{
    public function __construct(
        private NodeRepositoryInterface $repository,
        private EventBusInterface $eventBus,
        private AggregateActionsChainInterface $actionsChain,
        private CanDeleteNodeInterface $canDeleteNode
    ) {
    }

    public function __invoke(string $id): void
    {
        $node = $this->repository->get($id);

        if (!$node) {
            return;
        }

        $this->actionsChain->execute('delete', $node);
        $node->delete($this->canDeleteNode);

        $this->repository->delete($node);
        $this->eventBus->dispatchCollection($node->collectDomainEvents());
    }
}
