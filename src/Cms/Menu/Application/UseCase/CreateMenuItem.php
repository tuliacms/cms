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
        private MenuRepositoryInterface $repository,
        private EventBusInterface $eventBus
    ) {
    }

    /**
     * @param RequestInterface&CreateMenuItemRequest $request
     */
    protected function execute(RequestInterface $request): ?ResultInterface
    {
        $menu = $this->repository->get($request->menuId);

        $item = $menu->createItem();
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

        $translation = $item->translate($request->locale);
        $translation->renameTo($request->details['name']);

        $this->repository->save($menu);
        $this->eventBus->dispatchCollection($menu->collectDomainEvents());

        return null;
    }
}
