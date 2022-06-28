<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine;

use Doctrine\DBAL\Connection;
use Tulia\Cms\Shared\Application\UseCase\ResultInterface;
use Tulia\Cms\Shared\Application\UseCase\TransactionalSessionInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class DoctrineTransactionalSession implements TransactionalSessionInterface
{
    public function __construct(
        private Connection $connection
    ) {
    }

    public function executeAtomically(callable $operation): ?ResultInterface
    {
        return $this->connection->transactional($operation);
    }
}
