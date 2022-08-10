<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Application\UseCase;

use Tulia\Cms\Shared\Application\UseCase\AbstractTransactionalUseCase;
use Tulia\Cms\Shared\Application\UseCase\RequestInterface;
use Tulia\Cms\Shared\Application\UseCase\ResultInterface;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\Widget\Domain\WriteModel\WidgetRepositoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class DeleteWidget extends AbstractTransactionalUseCase
{
    public function __construct(
        private WidgetRepositoryInterface $repository,
        private EventBusInterface $eventBus
    ) {
    }

    /**
     * @param RequestInterface&DeleteWidgetRequest $request
     */
    protected function execute(RequestInterface $request): ?ResultInterface
    {
        $widget = $this->repository->get($request->id);

        $this->repository->delete($widget);
        $this->eventBus->dispatchCollection($widget->collectDomainEvents());

        return null;
    }
}
