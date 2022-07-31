<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Application\UseCase;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Tulia\Cms\Security\Framework\Security\Core\User\User as CoreUser;
use Tulia\Cms\Shared\Application\UseCase\IdResult;
use Tulia\Cms\Shared\Application\UseCase\RequestInterface;
use Tulia\Cms\Shared\Application\UseCase\ResultInterface;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\User\Application\Service\Avatar\UploaderInterface;
use Tulia\Cms\User\Domain\WriteModel\Model\User;
use Tulia\Cms\User\Domain\WriteModel\UserRepositoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class CreateUser extends AbstractUserUseCase
{
    public function __construct(
        UserRepositoryInterface $repository,
        EventBusInterface $eventDispatcher,
        private UserPasswordHasherInterface $passwordHasher,
        private UploaderInterface $uploader,
    ) {
        parent::__construct($repository, $eventDispatcher);
    }

    /**
     * @param RequestInterface&CreateUserRequest $request
     */
    public function execute(RequestInterface $request): ?ResultInterface
    {
        $securityUser = new CoreUser($request->email, null, $request->roles);
        $hashedPassword = $this->passwordHasher->hashPassword($securityUser, $request->password);

        $user = User::create(
            $this->repository->getNextId(),
            $request->email,
            $hashedPassword,
            $request->roles,
            $request->enabled,
            $request->locale,
            $request->attributes,
            $request->name
        );

        if ($request->avatar) {
            $user->changeAvatar($this->uploader->upload($request->avatar));
        }

        $this->create($user);

        return new IdResult($user->getId());
    }
}
