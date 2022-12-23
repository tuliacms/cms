<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Application\UseCase;

use Tulia\Cms\Shared\Application\UseCase\AbstractTransactionalUseCase;
use Tulia\Cms\Shared\Application\UseCase\RequestInterface;
use Tulia\Cms\Shared\Application\UseCase\ResultInterface;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Service\TaxonomyRepositoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class UpdateTermsHierarchy extends AbstractTransactionalUseCase
{
    public function __construct(
        private readonly TaxonomyRepositoryInterface $repository,
        private readonly EventBusInterface $bus,
    ) {
    }

    /**
     * @param RequestInterface&UpdateTermsHierarchyRequest $request
     */
    protected function execute(RequestInterface $request): ?ResultInterface
    {
        $taxonomy = $this->repository->get(
            $request->taxonomyType,
            $request->websiteId,
        );

        $taxonomy->updateHierarchy($request->hierarchy);

        $this->repository->save($taxonomy);
        $this->bus->dispatchCollection($taxonomy->collectDomainEvents());

        return null;
    }
}
