<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Application\UseCase;

use Tulia\Cms\Shared\Application\UseCase\AbstractTransactionalUseCase;
use Tulia\Cms\Shared\Application\UseCase\RequestInterface;
use Tulia\Cms\Shared\Application\UseCase\ResultInterface;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\Website\Domain\WriteModel\Rules\CanDeleteWebsite\CanDeleteWebsiteInterface;
use Tulia\Cms\Website\Domain\WriteModel\WebsiteRepositoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class DeleteWebsite extends AbstractTransactionalUseCase
{
    public function __construct(
        private readonly WebsiteRepositoryInterface $repository,
        private readonly EventBusInterface $eventBus,
        private readonly CanDeleteWebsiteInterface $canDeleteWebsite
    ) {
    }

    /**
     * @param RequestInterface&DeleteWebsiteRequest $request
     */
    protected function execute(RequestInterface $request): ?ResultInterface
    {
        $this->denyIfNotDevelopmentEnvironment();

        $website = $this->repository->get($request->id);
        $website->delete($this->canDeleteWebsite);

        $this->repository->delete($website);
        $this->eventBus->dispatchCollection($website->collectDomainEvents());

        return null;
    }
}
