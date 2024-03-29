<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Application\UseCase;

use Tulia\Cms\Shared\Application\UseCase\AbstractTransactionalUseCase;
use Tulia\Cms\Shared\Application\UseCase\IdResult;
use Tulia\Cms\Shared\Application\UseCase\RequestInterface;
use Tulia\Cms\Shared\Application\UseCase\ResultInterface;
use Tulia\Cms\Shared\Domain\WriteModel\Service\SlugGeneratorStrategy\SlugGeneratorStrategyInterface;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Exception\TaxonomyNotExistsException;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Taxonomy;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Service\TaxonomyRepositoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class CreateTerm extends AbstractTransactionalUseCase
{
    public function __construct(
        private readonly SlugGeneratorStrategyInterface $slugGeneratorStrategy,
        private readonly TaxonomyRepositoryInterface $repository,
        private readonly EventBusInterface $bus,
    ) {
    }

    /**
     * @param RequestInterface&CreateTermRequest $request
     */
    protected function execute(RequestInterface $request): ?ResultInterface
    {
        try {
            $taxonomy = $this->repository->get(
                $request->taxonomyType,
                $request->websiteId,
            );
        } catch (TaxonomyNotExistsException $e) {
            $taxonomy = Taxonomy::create(
                $request->taxonomyType,
                $request->websiteId,
                $request->locales,
                $request->locale,
            );
        }

        $taxonomy->addTerm(
            $this->slugGeneratorStrategy,
            $termId = $this->repository->getNextId(),
            $request->data['name'],
            $request->data['slug'],
            $request->locales,
            $request->locale,
            $request->defaultLocale,
            $request->data['attributes'],
            $request->data['parent'],
        );

        $this->repository->save($taxonomy);
        $this->bus->dispatchCollection($taxonomy->collectDomainEvents());

        return new IdResult($termId);
    }
}
