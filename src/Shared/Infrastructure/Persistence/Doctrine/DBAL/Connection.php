<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL;

use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\DBAL\Connection as DoctrineConnection;
use Doctrine\DBAL\Result;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\Schema\SchemaManager;

/**
 * @author Adam Banaszkiewicz
 */
class Connection extends DoctrineConnection
{
    public function executeQuery(
        string $sql,
        array $params = [],
        $types = [],
        ?QueryCacheProfile $qcp = null
    ): Result {
        return parent::executeQuery($this->prepareTablePrefix($sql), $params, $types, $qcp);
    }

    public function prepareTablePrefix(string $query): string
    {
        if (! isset($_ENV['DATABASE_PREFIX'])) {
            throw new \RuntimeException('Missing DATABASE_PREFIX env variable. Did You forget to define it in .env file?');
        }

        return str_replace('#__', $_ENV['DATABASE_PREFIX'], $query);
    }

    public function getSchemaManager(): SchemaManager
    {
        return new SchemaManager($this, parent::getSchemaManager(), $this->getDatabasePlatform());
    }
}
