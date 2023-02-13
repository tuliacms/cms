<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Application\UseCase;

use Tulia\Cms\Node\Domain\WriteModel\Service\NodeRepositoryInterface;
use Tulia\Cms\Shared\Application\UseCase\AbstractTransactionalUseCase;
use Tulia\Cms\Shared\Application\UseCase\IdResult;
use Tulia\Cms\Shared\Application\UseCase\RequestInterface;
use Tulia\Cms\Shared\Application\UseCase\ResultInterface;
use Tulia\Cms\Shared\Domain\WriteModel\Service\SlugGeneratorStrategy\SlugGeneratorStrategyInterface;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class CloneNode extends AbstractTransactionalUseCase
{
    public function __construct(
        protected readonly NodeRepositoryInterface $repository,
        protected readonly EventBusInterface $eventBus,
        protected readonly SlugGeneratorStrategyInterface $slugGeneratorStrategy,
    ) {
    }

    /**
     * @param RequestInterface&CloneNodeRequest $request
     */
    protected function execute(RequestInterface $request): ?ResultInterface
    {
        $node = $this->repository->get($request->id);
        $clone = $node->clone($this->repository->getNextId(), $this->slugGeneratorStrategy);

        $this->repository->save($clone);
        $this->eventBus->dispatchCollection($clone->collectDomainEvents());

        return new IdResult($clone->getId());
    }
}
