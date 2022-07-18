<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Application\UseCase;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Tulia\Cms\Security\Framework\Security\Core\User\User as CoreUser;
use Tulia\Cms\Shared\Application\UseCase\RequestInterface;
use Tulia\Cms\Shared\Application\UseCase\ResultInterface;
use Tulia\Cms\Shared\Domain\WriteModel\ActionsChain\AggregateActionsChainInterface;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\User\Application\Service\Avatar\UploaderInterface;
use Tulia\Cms\User\Domain\WriteModel\UserRepositoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class UpdateUser extends AbstractUserUseCase
{
    public function __construct(
        UserRepositoryInterface $repository,
        EventBusInterface $eventDispatcher,
        AggregateActionsChainInterface $actionsChain,
        private UserPasswordHasherInterface $passwordHasher,
        private UploaderInterface $uploader,
    ) {
        parent::__construct($repository, $eventDispatcher, $actionsChain);
    }

    /**
     * @param RequestInterface&UpdateUserRequest $request
     */
    public function execute(RequestInterface $request): ?ResultInterface
    {
        $user = $this->repository->get($request->id);

        $user->updateAttributes($request->attributes);
        $user->persistRoles($request->roles);
        $user->changeLocale($request->locale);
        $user->changeName($request->name);

        if ($request->enabled) {
            $user->enableAccount();
        } else {
            $user->disableAccount();
        }

        if (false === empty($request->password)) {
            $securityUser = new CoreUser($request->email, null, $request->roles);
            $hashedPassword = $this->passwordHasher->hashPassword($securityUser, $request->password);
            $user->changePassword($hashedPassword);
        }

        if ($request->removeAvatar) {
            $user->removeAvatar($this->uploader);
        }

        if ($request->avatar) {
            $user->changeAvatar($this->uploader->upload($request->avatar));
        }

        $this->update($user);

        return null;
    }
}
