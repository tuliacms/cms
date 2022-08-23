<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Application\UseCase;

use Tulia\Cms\Node\Domain\WriteModel\Rules\CanDeleteNode\CanDeleteNodeInterface;
use Tulia\Cms\Node\Domain\WriteModel\Service\NodeRepositoryInterface;
use Tulia\Cms\Shared\Application\UseCase\AbstractTransactionalUseCase;
use Tulia\Cms\Shared\Application\UseCase\RequestInterface;
use Tulia\Cms\Shared\Application\UseCase\ResultInterface;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class DeleteNode extends AbstractTransactionalUseCase
{
    public function __construct(
        private NodeRepositoryInterface $repository,
        private EventBusInterface $eventBus,
        private CanDeleteNodeInterface $canDeleteNode
    ) {
    }

    protected function execute(RequestInterface $request): ?ResultInterface
    {
        $node = $this->repository->get($request->id);

        if (!$node) {
            return null;
        }

        $node->delete($this->canDeleteNode);

        $this->repository->delete($node);
        $this->eventBus->dispatchCollection($node->collectDomainEvents());

        return null;
    }
}
