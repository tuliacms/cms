<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Application\UseCase;

use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\SslModeEnum;
use Tulia\Cms\Shared\Application\UseCase\AbstractTransactionalUseCase;
use Tulia\Cms\Shared\Application\UseCase\RequestInterface;
use Tulia\Cms\Shared\Application\UseCase\ResultInterface;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\Website\Domain\WriteModel\Rules\CannAddLocale\CanAddLocale;
use Tulia\Cms\Website\Domain\WriteModel\Rules\CanTurnOffWebsite\CanTurnOffWebsiteInterface;
use Tulia\Cms\Website\Domain\WriteModel\WebsiteRepositoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class AddLocale extends AbstractTransactionalUseCase
{
    public function __construct(
        private readonly WebsiteRepositoryInterface $repository,
        private readonly EventBusInterface $eventBus,
        private readonly CanTurnOffWebsiteInterface $canTurnOffWebsite
    ) {
    }

    /**
     * @param RequestInterface&AddLocaleRequest $request
     */
    protected function execute(RequestInterface $request): ?ResultInterface
    {
        $website = $this->repository->get($request->websiteId);
        $website->addLocale(
            new CanAddLocale(),
            $request->code,
            $request->domain,
            $request->domainDevelopment,
            $request->localePrefix,
            $request->pathPrefix,
            SslModeEnum::tryFrom($request->sslMode),
        );

        $this->repository->save($website);
        $this->eventBus->dispatchCollection($website->collectDomainEvents());

        return null;
    }
}
