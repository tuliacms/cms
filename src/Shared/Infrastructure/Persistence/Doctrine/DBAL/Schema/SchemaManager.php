<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\Schema;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Column;

/**
 * @author Adam Banaszkiewicz
 */
class SchemaManager extends AbstractSchemaManager
{
    protected AbstractSchemaManager $originalManager;

    public function __construct(Connection $conn, AbstractSchemaManager $originalManager, ?AbstractPlatform $platform = null)
    {
        parent::__construct($conn, $platform);

        $this->originalManager = $originalManager;
    }

    public function tablesExist($tableNames): bool
    {
        return parent::tablesExist(array_map([$this->_conn, 'prepareTablePrefix'], (array) $tableNames));
    }

    protected function _getPortableTableColumnDefinition($tableColumn): Column
    {
        return $this->originalManager->_getPortableTableColumnDefinition($tableColumn);
    }

    protected function _getPortableTableIndexesList($tableIndexRows, $tableName = null): array
    {
        foreach ($tableIndexRows as $key => $details) {
            foreach ($details as $name => $val) {
                $tableIndexRows[$key][strtolower($name)] = $val;

                if ($name === 'Key_name' && $val === 'PRIMARY') {
                    $tableIndexRows[$key]['primary'] = true;
                }
            }
        }

        return parent::_getPortableTableIndexesList($tableIndexRows, $tableName);
    }

    /**
     * Temporary fix for Doctrine Migrations 3.0.1.
     * Get table name from table array details returned by MySQL.
     *
     * @TODO Remove this method when DM fixed this problem.
     */
    protected function _getPortableTableDefinition($table): ?string
    {
        return $table['Tables_in_' . $this->_conn->getParams()['dbname']] ?? null;
    }
}
