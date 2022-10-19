<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Application\UseCase;

use Tulia\Cms\Shared\Application\UseCase\AbstractTransactionalUseCase;
use Tulia\Cms\Shared\Application\UseCase\RequestInterface;
use Tulia\Cms\Shared\Application\UseCase\ResultInterface;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\Widget\Domain\WriteModel\Model\Widget;
use Tulia\Cms\Widget\Domain\WriteModel\WidgetRepositoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class UpdateWidget extends AbstractTransactionalUseCase
{
    public function __construct(
        private readonly WidgetRepositoryInterface $repository,
        private readonly EventBusInterface $eventBus,
    ) {
    }

    /**
     * @param RequestInterface&UpdateWidgetRequest $request
     */
    protected function execute(RequestInterface $request): ?ResultInterface
    {
        $widget = $this->repository->get($request->id);

        $widget->rename($request->details['name']);
        $widget->setHtmlId($request->details['htmlId']);
        $widget->setHtmlClass($request->details['htmlClass']);
        $widget->updateStyles($request->details['styles']);

        if ($request->details['visibility']) {
            $widget->turnVisibilityOn($request->locale, $request->defaultLocale);
        } else {
            $widget->turnVisibilityOff($request->locale, $request->defaultLocale);
        }

        $widget->changeTitle($request->locale, $request->defaultLocale, $request->details['title']);
        $widget->persistAttributes($request->locale, $request->defaultLocale, $request->attributes);
        $widget->moveToSpace($request->details['space']);

        $this->repository->save($widget);
        $this->eventBus->dispatchCollection($widget->collectDomainEvents());

        return null;
    }
}
