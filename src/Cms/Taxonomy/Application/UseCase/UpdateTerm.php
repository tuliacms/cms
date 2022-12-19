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
final class UpdateTerm extends AbstractTransactionalUseCase
{
    public function __construct(
        private readonly TaxonomyRepositoryInterface $repository,
        private readonly EventBusInterface $bus,
    ) {
    }

    /**
     * @param RequestInterface&UpdateTermRequest $request
     */
    protected function execute(RequestInterface $request): ?ResultInterface
    {
        $taxonomy = $this->repository->get(
            $request->taxonomyType,
            $request->websiteId,
            $request->locales,
            $request->locale,
        );

        $taxonomy->updateTerm(
            $request->termId,
            $request->locale,
            $request->details['name'],
            $request->defaultLocale,
            $request->attributes,
            //$request->details['parent'],
        );

        $this->repository->save($taxonomy);
        $this->bus->dispatchCollection($taxonomy->collectDomainEvents());

        return null;
    }
}
