<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder as DoctrineQueryBuilder;

/**
 * @author Adam Banaszkiewicz
 */
class QueryBuilder extends DoctrineQueryBuilder
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        parent::__construct($connection);

        $this->connection = $connection;
    }

    public function compileSQL(): string
    {
        $sql = $this->getSQL();

        foreach ($this->getParameters() as $parameter => $value) {
            if (is_numeric($value)) {
                $sql = str_replace(":$parameter", $value, $sql);
            } elseif ($value === null) {
                $sql = str_replace(":$parameter", 'NULL', $sql);
            } else{
                $sql = str_replace(":$parameter", $this->connection->quote($value), $sql);
            }
        }

        return $this->connection->prepareTablePrefix($sql);
    }
}
