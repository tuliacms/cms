<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Application\UseCase;

use Tulia\Cms\Filemanager\Domain\WriteModel\DirectoryRepositoryInterface;
use Tulia\Cms\Filemanager\Domain\WriteModel\Service\FileStorageInterface;
use Tulia\Cms\Shared\Application\UseCase\AbstractTransactionalUseCase;
use Tulia\Cms\Shared\Application\UseCase\IdResult;
use Tulia\Cms\Shared\Application\UseCase\RequestInterface;
use Tulia\Cms\Shared\Application\UseCase\ResultInterface;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class UploadFile extends AbstractTransactionalUseCase
{
    public function __construct(
        private readonly FileStorageInterface $fileStorage,
        private readonly DirectoryRepositoryInterface $repository,
        private readonly EventBusInterface $eventBus,
    ) {
    }

    /**
     * @param RequestInterface&UploadFileRequest $request
     */
    protected function execute(RequestInterface $request): ?ResultInterface
    {
        $fileId = $this->repository->getNextId();

        $directory = $this->repository->get($request->directory);
        $directory->place($this->fileStorage, $fileId, $request->filepath);

        $this->repository->save($directory);
        $this->eventBus->dispatchCollection($directory->collectDomainEvents());

        return new IdResult($fileId);
    }
}
