<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Application\UseCase;

use Tulia\Cms\Shared\Application\UseCase\AbstractTransactionalUseCase;
use Tulia\Cms\Shared\Application\UseCase\RequestInterface;
use Tulia\Cms\Shared\Application\UseCase\ResultInterface;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\User\Domain\WriteModel\Service\PasswordHasherInterface;
use Tulia\Cms\User\Domain\WriteModel\UserRepositoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class ChangePassword extends AbstractTransactionalUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $repository,
        private readonly EventBusInterface $eventDispatcher,
        private readonly PasswordHasherInterface $passwordHasher,
    ) {
    }

    /**
     * @param RequestInterface&ChangePasswordRequest $request
     */
    public function execute(RequestInterface $request): ?ResultInterface
    {
        $user = $this->repository->get($request->userId);
        $user->changePassword($this->passwordHasher, $request->newPassword);

        $this->repository->save($user);
        $this->eventDispatcher->dispatchCollection($user->collectDomainEvents());

        return null;
    }
}
