<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Application\UseCase;

use Tulia\Cms\Shared\Application\UseCase\RequestInterface;
use Tulia\Cms\Shared\Application\UseCase\ResultInterface;
use Tulia\Cms\Shared\Domain\WriteModel\ActionsChain\AggregateActionsChainInterface;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\User\Domain\WriteModel\Service\PasswordHasherInterface;
use Tulia\Cms\User\Domain\WriteModel\UserRepositoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class ChangePassword extends AbstractUserUseCase
{
    public function __construct(
        UserRepositoryInterface $repository,
        EventBusInterface $eventDispatcher,
        AggregateActionsChainInterface $actionsChain,
        private PasswordHasherInterface $passwordHasher
    ) {
        parent::__construct($repository, $eventDispatcher, $actionsChain);
    }

    /**
     * @param RequestInterface&ChangePasswordRequest $request
     */
    public function execute(RequestInterface $request): ?ResultInterface
    {
        $user = $this->repository->get($request->userId);
        $user->changePassword($this->passwordHasher, $request->newPassword);
        $this->update($user);

        return null;
    }
}
