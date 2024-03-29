<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Application\UseCase;

use Tulia\Cms\Shared\Application\UseCase\AbstractTransactionalUseCase;
use Tulia\Cms\Shared\Application\UseCase\RequestInterface;
use Tulia\Cms\Shared\Application\UseCase\ResultInterface;
use Tulia\Cms\Shared\Domain\WriteModel\Service\SlugGeneratorStrategy\SlugGeneratorStrategyInterface;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Service\TaxonomyRepositoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class UpdateTerm extends AbstractTransactionalUseCase
{
    public function __construct(
        private readonly SlugGeneratorStrategyInterface $slugGeneratorStrategy,
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
        );

        $taxonomy->updateTerm(
            $this->slugGeneratorStrategy,
            $request->termId,
            $request->locale,
            $request->data['name'],
            $request->data['slug'],
            $request->defaultLocale,
            $request->data['attributes'],
        );

        if ($request->data['visibility']) {
            $taxonomy->turnTermVisibilityOn($request->termId, $request->locale, $request->defaultLocale);
        } else {
            $taxonomy->turnTermVisibilityOff($request->termId, $request->locale, $request->defaultLocale);
        }

        $this->repository->save($taxonomy);
        $this->bus->dispatchCollection($taxonomy->collectDomainEvents());

        return null;
    }
}
