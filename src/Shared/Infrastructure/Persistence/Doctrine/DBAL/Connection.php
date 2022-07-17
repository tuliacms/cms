<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL;

use Doctrine\DBAL\Query\QueryBuilder;
use Traversable;
use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\DBAL\Connection as DoctrineConnection;
use Doctrine\DBAL\Result;
use Doctrine\DBAL\Statement;
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

    public function executeStatement($sql, array $params = [], array $types = [])
    {
        return parent::executeQuery($this->prepareTablePrefix($sql), $params, $types);
    }

    public function prepareTablePrefix(string $query): string
    {
        if (! isset($_ENV['DATABASE_PREFIX'])) {
            throw new \RuntimeException('Missing DATABASE_PREFIX env variable. Did You forget to define it in .env file?');
        }

        return str_replace('#__', $_ENV['DATABASE_PREFIX'], $query);
    }

    public function update($table, array $data, array $criteria, array $types = [])
    {
        return parent::update($this->prepareTablePrefix($table), $data, $criteria, $types);
    }

    public function insert($table, array $data, array $types = [])
    {
        return parent::insert($this->prepareTablePrefix($table), $data, $types);
    }

    public function delete($table, array $criteria, array $types = [])
    {
        return parent::delete($this->prepareTablePrefix($table), $criteria, $types);
    }

    public function fetchAssociative(string $query, array $params = [], array $types = [])
    {
        return parent::fetchAssociative($this->prepareTablePrefix($query), $params, $types);
    }

    public function fetchNumeric(string $query, array $params = [], array $types = [])
    {
        return parent::fetchNumeric($this->prepareTablePrefix($query), $params, $types);
    }

    public function fetchOne(string $query, array $params = [], array $types = [])
    {
        return parent::fetchOne($this->prepareTablePrefix($query), $params, $types);
    }

    public function fetchAllNumeric(string $query, array $params = [], array $types = []): array
    {
        return parent::fetchAllNumeric($this->prepareTablePrefix($query), $params, $types);
    }

    public function fetchAllAssociative(string $query, array $params = [], array $types = []): array
    {
        return parent::fetchAllAssociative($this->prepareTablePrefix($query), $params, $types);
    }

    public function fetchAllKeyValue(string $query, array $params = [], array $types = []): array
    {
        return parent::fetchAllKeyValue($this->prepareTablePrefix($query), $params, $types);
    }

    public function fetchAllAssociativeIndexed(string $query, array $params = [], array $types = []): array
    {
        return parent::fetchAllAssociativeIndexed($this->prepareTablePrefix($query), $params, $types);
    }

    public function fetchFirstColumn(string $query, array $params = [], array $types = []): array
    {
        return parent::fetchFirstColumn($this->prepareTablePrefix($query), $params, $types);
    }

    public function iterateNumeric(string $query, array $params = [], array $types = []): Traversable
    {
        return parent::iterateNumeric($this->prepareTablePrefix($query), $params, $types);
    }

    public function iterateAssociative(string $query, array $params = [], array $types = []): Traversable
    {
        return parent::iterateAssociative($this->prepareTablePrefix($query), $params, $types);
    }

    public function iterateKeyValue(string $query, array $params = [], array $types = []): Traversable
    {
        return parent::iterateKeyValue($this->prepareTablePrefix($query), $params, $types);
    }

    public function iterateAssociativeIndexed(string $query, array $params = [], array $types = []): Traversable
    {
        return parent::iterateAssociativeIndexed($this->prepareTablePrefix($query), $params, $types);
    }

    public function iterateColumn(string $query, array $params = [], array $types = []): Traversable
    {
        return parent::iterateColumn($this->prepareTablePrefix($query), $params, $types);
    }

    public function prepare(string $sql): Statement
    {
        return parent::prepare($this->prepareTablePrefix($sql));
    }

    public function createQueryBuilder(): QueryBuilder
    {
        return new Query\QueryBuilder($this);
    }

    public function getSchemaManager(): SchemaManager
    {
        return new SchemaManager($this, parent::getSchemaManager(), $this->getDatabasePlatform());
    }
}
