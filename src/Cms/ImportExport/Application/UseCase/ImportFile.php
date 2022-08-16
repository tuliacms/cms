<?php

declare(strict_types=1);

namespace Tulia\Cms\ImportExport\Application\UseCase;

use Tulia\Cms\Shared\Application\UseCase\AbstractTransactionalUseCase;
use Tulia\Cms\Shared\Application\UseCase\RequestInterface;
use Tulia\Cms\Shared\Application\UseCase\ResultInterface;
use Tulia\Component\Importer\ImporterInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class ImportFile extends AbstractTransactionalUseCase
{
    public function __construct(
        private ImporterInterface $importer
    ) {
    }

    /**
     * @param RequestInterface&ImportFileRequest $request
     */
    protected function execute(RequestInterface $request): ?ResultInterface
    {
        $this->importer->importFromFile($request->filepath, $request->originalName);

        return null;
    }
}
