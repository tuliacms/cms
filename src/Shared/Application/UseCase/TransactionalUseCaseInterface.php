<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Application\UseCase;

/**
 * @author Adam Banaszkiewicz
 */
interface TransactionalUseCaseInterface
{
    public function __invoke(RequestInterface $request): ?ResultInterface;
    public function setTransactionalSession(TransactionalSessionInterface $transactionalSession): void;
}
