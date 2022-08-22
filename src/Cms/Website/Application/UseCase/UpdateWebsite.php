<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Application\UseCase;

use Tulia\Cms\Shared\Application\UseCase\AbstractTransactionalUseCase;
use Tulia\Cms\Shared\Application\UseCase\RequestInterface;
use Tulia\Cms\Shared\Application\UseCase\ResultInterface;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\Website\Domain\WriteModel\Rules\CanTurnOffWebsite\CanTurnOffWebsiteInterface;
use Tulia\Cms\Website\Domain\WriteModel\WebsiteRepositoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class UpdateWebsite extends AbstractTransactionalUseCase
{
    public function __construct(
        private readonly WebsiteRepositoryInterface $repository,
        private readonly EventBusInterface $eventBus,
        private readonly CanTurnOffWebsiteInterface $canTurnOffWebsite
    ) {
    }

    /**
     * @param RequestInterface&UpdateWebsiteRequest $request
     */
    protected function execute(RequestInterface $request): ?ResultInterface
    {
        $website = $this->repository->get($request->id);
        $website->rename($request->name);

        if ($request->active) {
            $website->turnOn();
        } else {
            $website->turnOff($this->canTurnOffWebsite);
        }

        $website->replaceLocales($request->locales);

        $this->repository->save($website);
        $this->eventBus->dispatchCollection($website->collectDomainEvents());

        return null;
    }
}
