<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Application\UseCase;

/**
 * @author Adam Banaszkiewicz
 */
interface TransactionalSessionInterface
{
    public function executeAtomically(callable $operation): ?ResultInterface;
}
