<?php

declare(strict_types=1);

namespace Tulia\Cms\ImportExport\Application\UseCase;

use Tulia\Cms\Shared\Application\UseCase\AbstractTransactionalUseCase;
use Tulia\Cms\Shared\Application\UseCase\RequestInterface;
use Tulia\Cms\Shared\Application\UseCase\ResultInterface;
use Tulia\Component\Importer\ImporterInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteRegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class ImportFile extends AbstractTransactionalUseCase
{
    public function __construct(
        private readonly ImporterInterface $importer,
        private readonly WebsiteRegistryInterface $websiteRegistry,
    ) {
    }

    /**
     * @param RequestInterface&ImportFileRequest $request
     */
    protected function execute(RequestInterface $request): ?ResultInterface
    {
        $this->importer->importFromFile(
            $request->filepath,
            $request->originalName,
            [
                'website' => $this->websiteRegistry->get($request->websiteId),
                'author_id' => $request->authorId,
            ]
        );

        return null;
    }
}
