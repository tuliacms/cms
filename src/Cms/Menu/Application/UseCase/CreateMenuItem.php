<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Application\UseCase;

use Tulia\Cms\Menu\Domain\WriteModel\MenuRepositoryInterface;
use Tulia\Cms\Shared\Application\UseCase\AbstractTransactionalUseCase;
use Tulia\Cms\Shared\Application\UseCase\IdResult;
use Tulia\Cms\Shared\Application\UseCase\RequestInterface;
use Tulia\Cms\Shared\Application\UseCase\ResultInterface;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;

/**
 * @author Adam Banaszkiewicz
 */
class CreateMenuItem extends AbstractTransactionalUseCase
{
    public function __construct(
        private readonly MenuRepositoryInterface $repository,
        private readonly EventBusInterface $eventBus,
    ) {
    }

    /**
     * @param RequestInterface&CreateMenuItemRequest $request
     */
    protected function execute(RequestInterface $request): ?ResultInterface
    {
        $menu = $this->repository->get($request->menuId);

        if ($request->data['parent']) {
            $item = $menu->createChildItem($request->data['parent'], $request->availableLocales, $request->locale, $request->data['name']);
        } else {
            $item = $menu->createItem($request->availableLocales, $request->locale, $request->data['name']);
        }

        $item->linksTo(
            (string) $request->data['type'],
            (string) $request->data['identity'],
            (string) $request->data['hash']
        );

        if ($request->data['target'] === '_blank') {
            $item->openInNewTab();
        } else {
            $item->openInSelfTab();
        }

        $this->repository->save($menu);
        $this->eventBus->dispatchCollection($menu->collectDomainEvents());

        return new IdResult($item->getId());
    }
}
