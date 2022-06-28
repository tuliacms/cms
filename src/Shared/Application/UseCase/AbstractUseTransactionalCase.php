<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Application\UseCase;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractUseTransactionalCase implements TransactionalUseCaseInterface
{
    private TransactionalSessionInterface $transactionalSession;

    public function __invoke(RequestInterface $request): ?ResultInterface
    {
        return $this->transactionalSession->executeAtomically(function () use ($request) {
            return $this->execute($request);
        });
    }

    public function setTransactionalSession(TransactionalSessionInterface $transactionalSession): void
    {
        $this->transactionalSession = $transactionalSession;
    }

    abstract protected function execute(RequestInterface $request): ?ResultInterface;
}
