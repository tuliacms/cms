<?php

declare(strict_types=1);

namespace Tulia\Cms\Options\Infrastructure\Persistence\ReadModel\Options;

use Doctrine\DBAL\Connection;
use Tulia\Cms\Options\Domain\ReadModel\OptionsFinderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalOptionsFinder implements OptionsFinderInterface
{
    public function __construct(
        private Connection $connection
    ) {
    }

    public function findByName(string $name, string $locale)
    {
        $result = $this->connection->fetchOne('SELECT COALESCE(tl.`value`, tm.`value`) AS `value`
            FROM #__option tm
            LEFT JOIN #__option_lang tl
                ON tm.id = tl.option_id AND tl.locale = :locale
            WHERE tm.`name` = :name
            LIMIT 1', [
            'name'      => $name,
            'locale'    => $locale,
        ], [
            'name'      => \PDO::PARAM_STR,
            'locale'    => \PDO::PARAM_STR,
            'websiteId' => \PDO::PARAM_STR,
        ]);

        return \is_bool($result) ? null : $result;
    }

    public function findBulkByName(array $names, string $locale): array
    {
        $result = $this->connection->fetchAllAssociative('SELECT tm.name, COALESCE(tl.`value`, tm.`value`) AS `value`
            FROM #__option tm
            LEFT JOIN #__option_lang tl
                ON tm.id = tl.option_id AND tl.locale = :locale
            WHERE tm.`name` IN (:name)
            LIMIT 1', [
            'name'      => $names,
            'locale'    => $locale,
        ], [
            'name'      => Connection::PARAM_STR_ARRAY,
            'locale'    => \PDO::PARAM_STR,
            'websiteId' => \PDO::PARAM_STR,
        ]);

        return array_column($result, 'value', 'name');
    }

    public function autoload(string $locale): array
    {
        $result = $this->connection->fetchAllAssociative('SELECT tm.name, COALESCE(tl.`value`, tm.`value`) AS `value`
            FROM #__option tm
            LEFT JOIN #__option_lang tl
                ON tm.id = tl.option_id AND tl.locale = :locale
            WHERE tm.autoload = 1', [
            'locale'    => $locale,
        ], [
            'locale'    => \PDO::PARAM_STR,
            'websiteId' => \PDO::PARAM_STR,
        ]);

        return array_column($result, 'value', 'name');
    }
}
