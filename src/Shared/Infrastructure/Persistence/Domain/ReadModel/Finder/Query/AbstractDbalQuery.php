<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\Query;

use Doctrine\DBAL\Query\QueryBuilder;
use Exception;
use Doctrine\DBAL\Driver\Exception as DoctrineException;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\Exception\QueryException;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractDbalQuery extends AbstractQuery
{
    public const STORAGE_NAME = 'doctrine.query-builder';

    public function getSupportedStorage(): string
    {
        return self::STORAGE_NAME;
    }
}
