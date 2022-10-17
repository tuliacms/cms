<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Application\UseCase;

use Tulia\Cms\Shared\Application\UseCase\AbstractTransactionalUseCase;
use Tulia\Cms\Shared\Application\UseCase\RequestInterface;
use Tulia\Cms\Shared\Application\UseCase\ResultInterface;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\User\Application\Service\Avatar\UploaderInterface;
use Tulia\Cms\User\Domain\WriteModel\Rules\CanDeleteUser\CanDeleteUserInterface;
use Tulia\Cms\User\Domain\WriteModel\UserRepositoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class DeleteUser extends AbstractTransactionalUseCase
{
    public function __construct(
        private readonly UploaderInterface $uploader,
        private readonly UserRepositoryInterface $repository,
        private readonly EventBusInterface $eventDispatcher,
        private readonly CanDeleteUserInterface $canDeleteUser,
    ) {
    }

    /**
     * @param RequestInterface&DeleteUserRequest $request
     */
    protected function execute(RequestInterface $request): ?ResultInterface
    {
        $user = $this->repository->get($request->id);

        $user->delete($this->canDeleteUser, $this->uploader);
        $this->repository->delete($user);

        $this->eventDispatcher->dispatchCollection($user->collectDomainEvents());

        return null;
    }
}
