<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Application\UseCase;

use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\SslModeEnum;
use Tulia\Cms\Shared\Application\UseCase\AbstractTransactionalUseCase;
use Tulia\Cms\Shared\Application\UseCase\IdResult;
use Tulia\Cms\Shared\Application\UseCase\RequestInterface;
use Tulia\Cms\Shared\Application\UseCase\ResultInterface;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\Website\Domain\WriteModel\Model\Website;
use Tulia\Cms\Website\Domain\WriteModel\WebsiteRepositoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class CreateWebsite extends AbstractTransactionalUseCase
{
    public function __construct(
        private readonly WebsiteRepositoryInterface $repository,
        private readonly EventBusInterface $eventBus,
    ) {
    }

    /**
     * @param RequestInterface&CreateWebsiteRequest $request
     */
    protected function execute(RequestInterface $request): ?ResultInterface
    {
        $website = Website::create(
            $this->repository->getNextId(),
            $request->name,
            $request->locale,
            $request->domain,
            $request->domainDevelopment,
            $request->backendPrefix,
            $request->pathPrefix,
            SslModeEnum::tryFrom($request->sslMode ?? SslModeEnum::ALLOWED_BOTH->value),
            $request->enabled,
        );

        $this->repository->save($website);
        $this->eventBus->dispatchCollection($website->collectDomainEvents());

        return new IdResult($website->getId());
    }
}
