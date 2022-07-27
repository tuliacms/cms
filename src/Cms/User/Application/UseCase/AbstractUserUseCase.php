<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Application\UseCase;

use Tulia\Cms\Shared\Application\UseCase\AbstractTransactionalUseCase;
use Tulia\Cms\Shared\Domain\WriteModel\ActionsChain\AggregateActionsChainInterface;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\User\Domain\WriteModel\Event\UserUpdated;
use Tulia\Cms\User\Domain\WriteModel\Model\User;
use Tulia\Cms\User\Domain\WriteModel\UserRepositoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractUserUseCase extends AbstractTransactionalUseCase
{
    public function __construct(
        protected UserRepositoryInterface $repository,
        protected EventBusInterface $eventDispatcher,
        protected AggregateActionsChainInterface $actionsChain
    ) {
    }

    protected function create(User $user): void
    {
        $this->actionsChain->execute('create', $user);

        $this->repository->save($user);
        $this->eventDispatcher->dispatchCollection($user->collectDomainEvents());
    }

    protected function update(User $user): void
    {
        $this->actionsChain->execute('update', $user);

        $this->repository->save($user);
        $this->eventDispatcher->dispatchCollection($user->collectDomainEvents());
        $this->eventDispatcher->dispatch(UserUpdated::fromModel($user));
    }
}
