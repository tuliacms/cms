<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Application\UseCase;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Tulia\Cms\Security\Framework\Security\Core\User\User as CoreUser;
use Tulia\Cms\Shared\Application\UseCase\AbstractTransactionalUseCase;
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
final class CreateUser extends AbstractTransactionalUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $repository,
        private readonly EventBusInterface $eventDispatcher,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly UploaderInterface $uploader,
    ) {
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

        $this->repository->save($user);
        $this->eventDispatcher->dispatchCollection($user->collectDomainEvents());

        return new IdResult($user->getId());
    }
}
