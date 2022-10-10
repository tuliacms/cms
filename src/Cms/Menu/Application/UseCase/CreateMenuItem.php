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

        if ($request->details['parent']) {
            $item = $menu->createChildItem($request->details['parent'], $request->availableLocales, $request->locale, $request->details['name']);
        } else {
            $item = $menu->createItem($request->availableLocales, $request->locale, $request->details['name']);
        }

        $item->linksTo(
            (string) $request->details['type'],
            (string) $request->details['identity'],
            (string) $request->details['hash']
        );

        if ($request->details['target'] === '_blank') {
            $item->openInNewTab();
        } else {
            $item->openInSelfTab();
        }

        $this->repository->save($menu);
        $this->eventBus->dispatchCollection($menu->collectDomainEvents());

        return null;
    }
}
