<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Application\UseCase;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractTransactionalUseCase implements TransactionalUseCaseInterface
{
    private string $environment;
    private TransactionalSessionInterface $transactionalSession;

    public function __invoke(RequestInterface $request): ?ResultInterface
    {
        return $this->transactionalSession->executeAtomically(function () use ($request) {
            return $this->execute($request);
        });
    }

    abstract protected function execute(RequestInterface $request): ?ResultInterface;

    public function setTransactionalSession(TransactionalSessionInterface $transactionalSession): void
    {
        $this->transactionalSession = $transactionalSession;
    }

    public function setEnvironment(string $environment): void
    {
        $this->environment = $environment;
    }

    /**
     * @throws AccessDeniedHttpException
     */
    protected function denyIfNotDevelopmentEnvironment(): void
    {
        if ($this->environment !== 'dev') {
            throw new AccessDeniedHttpException('Cannot execute this operation in not "dev" environment.');
        }
    }
}
