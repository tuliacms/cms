<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Application\UseCase;

use Tulia\Cms\Shared\Application\UseCase\RequestInterface;
use Tulia\Cms\Shared\Application\UseCase\ResultInterface;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\User\Application\Service\Avatar\UploaderInterface;
use Tulia\Cms\User\Domain\WriteModel\Service\PasswordHasherInterface;
use Tulia\Cms\User\Domain\WriteModel\UserRepositoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class UpdateUser extends AbstractUserUseCase
{
    public function __construct(
        UserRepositoryInterface $repository,
        EventBusInterface $eventDispatcher,
        private UploaderInterface $uploader,
        private PasswordHasherInterface $passwordHasher
    ) {
        parent::__construct($repository, $eventDispatcher);
    }

    /**
     * @param RequestInterface&UpdateUserRequest $request
     */
    public function execute(RequestInterface $request): ?ResultInterface
    {
        $user = $this->repository->get($request->id);

        $user->persistAttributes(...$request->attributes);
        $user->persistRoles($request->roles);
        $user->changeLocale($request->locale);
        $user->changeName($request->name);

        if ($request->enabled) {
            $user->enableAccount();
        } else {
            $user->disableAccount();
        }

        if (false === empty($request->password)) {
            $user->changePassword($this->passwordHasher, $request->password);
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
