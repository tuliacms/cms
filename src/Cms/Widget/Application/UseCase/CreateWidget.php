<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Application\UseCase;

use Tulia\Cms\Shared\Application\UseCase\AbstractTransactionalUseCase;
use Tulia\Cms\Shared\Application\UseCase\IdResult;
use Tulia\Cms\Shared\Application\UseCase\RequestInterface;
use Tulia\Cms\Shared\Application\UseCase\ResultInterface;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\Widget\Domain\WriteModel\Model\Widget;
use Tulia\Cms\Widget\Domain\WriteModel\WidgetRepositoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class CreateWidget extends AbstractTransactionalUseCase
{
    public function __construct(
        private WidgetRepositoryInterface $repository,
        private EventBusInterface $eventBus
    ) {
    }

    /**
     * @param RequestInterface&CreateWidgetRequest $request
     */
    protected function execute(RequestInterface $request): ?ResultInterface
    {
        $widget = Widget::create(
            $this->repository->getNextId(),
            $request->websiteId,
            $request->type,
            $request->details['space'],
            $request->details['name'],
            $request->locale,
            $request->localeCodes
        );

        if ($request->details['visibility']) {
            $widget->turnVisibilityOn($request->locale, $request->defaultLocale);
        } else {
            $widget->turnVisibilityOff($request->locale, $request->defaultLocale);
        }

        $widget->changeTitle($request->locale, $request->defaultLocale, $request->details['title']);
        $widget->persistAttributes($request->locale, $request->defaultLocale, $request->attributes);

        $this->repository->save($widget);
        $this->eventBus->dispatchCollection($widget->collectDomainEvents());

        return new IdResult($widget->getId());
    }
}
