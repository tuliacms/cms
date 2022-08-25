<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Application\UseCase;

use Tulia\Cms\Filemanager\Domain\WriteModel\DirectoryRepositoryInterface;
use Tulia\Cms\Filemanager\Domain\WriteModel\Service\FileStorageInterface;
use Tulia\Cms\Shared\Application\UseCase\AbstractTransactionalUseCase;
use Tulia\Cms\Shared\Application\UseCase\RequestInterface;
use Tulia\Cms\Shared\Application\UseCase\ResultInterface;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class DeleteFile extends AbstractTransactionalUseCase
{
    public function __construct(
        private readonly FileStorageInterface $fileStorage,
        private readonly DirectoryRepositoryInterface $repository,
        private readonly EventBusInterface $eventBus,
    ) {
    }

    /**
     * @param RequestInterface&DeleteFileRequest $request
     */
    protected function execute(RequestInterface $request): ?ResultInterface
    {
        $directory = $this->repository->getByFile($request->ids[0]);

        foreach ($request->ids as $id) {
            $directory->deleteFile($this->fileStorage, $id);
        }

        $this->repository->save($directory);
        $this->eventBus->dispatchCollection($directory->collectDomainEvents());

        return null;
    }
}
