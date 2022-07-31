<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Application\UseCase;

use Tulia\Cms\Shared\Application\UseCase\AbstractTransactionalUseCase;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\User\Domain\WriteModel\Model\User;
use Tulia\Cms\User\Domain\WriteModel\UserRepositoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractUserUseCase extends AbstractTransactionalUseCase
{
    public function __construct(
        protected UserRepositoryInterface $repository,
        protected EventBusInterface $eventDispatcher
    ) {
    }

    protected function create(User $user): void
    {
        $this->repository->save($user);
        $this->eventDispatcher->dispatchCollection($user->collectDomainEvents());
    }

    protected function update(User $user): void
    {
        $this->repository->save($user);
        $this->eventDispatcher->dispatchCollection($user->collectDomainEvents());
    }
}
