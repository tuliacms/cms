<?php

declare(strict_types=1);

namespace Tulia\Cms\Options\Infrastructure\Persistence\Doctrine\Dbal;

use Doctrine\DBAL\Connection;
use Symfony\Component\Uid\Uuid;
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

    public function findByName(string $name, string $websiteId, string $locale)
    {
        $result = $this->connection->fetchOne('SELECT COALESCE(tl.`value`, tm.`value`) AS `value`
            FROM #__option tm
            LEFT JOIN #__option_translation tl
                ON tm.id = tl.option_id AND tl.locale = :locale
            WHERE tm.`name` = :name AND tm.website_id = :website_id
            LIMIT 1', [
            'name'      => $name,
            'locale'    => $locale,
            'websiteId' => Uuid::fromString($websiteId)->toBinary(),
        ], [
            'name'      => \PDO::PARAM_STR,
            'locale'    => \PDO::PARAM_STR,
            'websiteId' => \PDO::PARAM_STR,
        ]);

        return \is_bool($result) ? null : $result;
    }

    public function findBulkByName(array $names, string $websiteId, string $locale): array
    {
        $result = $this->connection->fetchAllAssociative('SELECT tm.name, COALESCE(tl.`value`, tm.`value`) AS `value`
            FROM #__option tm
            LEFT JOIN #__option_translation tl
                ON tm.id = tl.option_id AND tl.locale = :locale
            WHERE tm.`name` IN (:name) AND tm.website_id = :website_id
            LIMIT 1', [
            'name'      => $names,
            'locale'    => $locale,
            'websiteId' => Uuid::fromString($websiteId)->toBinary(),
        ], [
            'name'      => Connection::PARAM_STR_ARRAY,
            'locale'    => \PDO::PARAM_STR,
            'websiteId' => \PDO::PARAM_STR,
        ]);

        return array_column($result, 'value', 'name');
    }

    public function autoload(string $websiteId, string $locale): array
    {
        $result = $this->connection->fetchAllAssociative('SELECT tm.name, COALESCE(tl.`value`, tm.`value`) AS `value`
            FROM #__option tm
            LEFT JOIN #__option_translation tl
                ON tm.id = tl.option_id AND tl.locale = :locale
            WHERE tm.website_id = :website_id AND tm.autoload = 1', [
            'locale'    => $locale,
            'website_id' => Uuid::fromString($websiteId)->toBinary(),
        ], [
            'locale'    => \PDO::PARAM_STR,
            'websiteId' => \PDO::PARAM_STR,
        ]);

        return array_column($result, 'value', 'name');
    }
}
