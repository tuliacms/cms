<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Application\UseCase;

use Tulia\Cms\Menu\Domain\WriteModel\MenuRepositoryInterface;
use Tulia\Cms\Shared\Application\UseCase\AbstractTransactionalUseCase;
use Tulia\Cms\Shared\Application\UseCase\RequestInterface;
use Tulia\Cms\Shared\Application\UseCase\ResultInterface;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class UpdateMenu extends AbstractTransactionalUseCase
{
    public function __construct(
        private readonly MenuRepositoryInterface $repository,
        private readonly EventBusInterface $eventBus,
    ) {
    }

    /**
     * @param RequestInterface&UpdateMenuRequest $request
     */
    protected function execute(RequestInterface $request): ?ResultInterface
    {
        $menu = $this->repository->get($request->id);
        $menu->rename($request->name);
        $menu->placeIn($request->spaces);

        $this->repository->save($menu);
        $this->eventBus->dispatchCollection($menu->collectDomainEvents());

        return null;
    }
}
