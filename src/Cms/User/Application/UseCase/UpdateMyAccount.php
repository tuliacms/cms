<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Application\UseCase;

use Tulia\Cms\Shared\Application\UseCase\AbstractTransactionalUseCase;
use Tulia\Cms\Shared\Application\UseCase\RequestInterface;
use Tulia\Cms\Shared\Application\UseCase\ResultInterface;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\User\Application\Service\Avatar\UploaderInterface;
use Tulia\Cms\User\Domain\WriteModel\UserRepositoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class UpdateMyAccount extends AbstractTransactionalUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $repository,
        private readonly EventBusInterface $eventDispatcher,
        private readonly UploaderInterface $uploader,
    ) {
    }

    /**
     * @param RequestInterface&UpdateMyAccountRequest $request
     */
    public function execute(RequestInterface $request): ?ResultInterface
    {
        $user = $this->repository->get($request->id);

        $user->persistAttributes(...$request->attributes);
        $user->changeLocale($request->locale);
        $user->changeName($request->name);

        if ($request->removeAvatar) {
            $user->removeAvatar($this->uploader);
        }

        if ($request->avatar) {
            $user->changeAvatar($this->uploader->upload($request->avatar));
        }

        $this->repository->save($user);
        $this->eventDispatcher->dispatchCollection($user->collectDomainEvents());

        return null;
    }
}
