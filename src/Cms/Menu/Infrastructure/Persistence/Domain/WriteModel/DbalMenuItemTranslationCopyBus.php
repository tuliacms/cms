<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Persistence\Domain\WriteModel;

use Doctrine\DBAL\Connection;
use Symfony\Component\Uid\Uuid;
use Tulia\Cms\Website\Domain\WriteModel\Service\TranslationCopyMachine\CopyBusInterface;

/**
 * @author Adam Banaszkiewicz
 * @final
 * @lazy
 */
class DbalMenuItemTranslationCopyBus implements CopyBusInterface
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    public function count(string $websiteId, string $defaultLocale): int
    {
        return (int) $this->connection->fetchOne('
            SELECT COUNT(mit.id)
            FROM #__menu_item_translation AS mit
            INNER JOIN #__menu_item AS mi
                ON mi.id = mit.item_id
            INNER JOIN #__menu AS m
                ON m.id = mi.menu_id AND m.website_id = :website
            WHERE mit.locale = :locale', [
            'website' => Uuid::fromString($websiteId)->toBinary(),
            'locale' => $defaultLocale,
        ]);
    }

    public function copy(string $websiteId, string $defaultLocale, string $targetLocale, int $offset, int $limit): int
    {
        $isList = $this->getMenuItemIdListPart($websiteId, $offset, $limit);
        $source = $this->getTranslationsSource($isList, $defaultLocale);

        foreach ($source as $item) {
            $id = Uuid::v4()->toBinary();

            $this->connection->insert('#__menu_item_translation', [
                'id' => $id,
                'item_id' => $item['item_id'],
                'name' => $item['name'],
                'locale' => $targetLocale,
                'visibility' => $item['visibility'],
                'translated' => 0,
            ]);
        }

        return count($source);
    }

    public function delete(string $websiteId, string $defaultLocale, string $targetLocale, int $offset, int $limit): int
    {
        $idList = $this->getMenuItemIdListPart($websiteId, $offset, $limit);
        $translationsIdList = array_column($this->getTranslationsSource($idList, $targetLocale), 'id');

        $this->connection->executeStatement(
            'DELETE FROM #__menu_item_translation WHERE id IN (:id)',
            ['id' => $translationsIdList],
            ['id' => Connection::PARAM_STR_ARRAY]
        );

        return count($idList);
    }

    private function getMenuItemIdListPart(string $websiteId, int $offset, int $limit): array
    {
        return $this->connection->fetchFirstColumn('
            SELECT mi.id
            FROM #__menu_item AS mi
            INNER JOIN #__menu AS m
                ON m.id = mi.menu_id AND m.website_id = :website
            ORDER BY mi.id ASC
            LIMIT :offset, :limit', [
            'website' => Uuid::fromString($websiteId)->toBinary(),
            'offset' => $offset,
            'limit' => $limit,
        ], [
            'offset' => \PDO::PARAM_INT,
            'limit' => \PDO::PARAM_INT,
        ]);
    }

    private function getTranslationsSource(array $itemIdList, string $locale): array
    {
        return $this->connection->fetchAllAssociative('
            SELECT mit.id, mit.item_id, mit.name, mit.locale, mit.visibility
            FROM #__menu_item_translation AS mit
            WHERE item_id IN (:ids) AND locale = :locale', [
            'locale' => $locale,
            'ids' => $itemIdList,
        ], [
            'ids' => Connection::PARAM_STR_ARRAY,
        ]);
    }
}
