<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Infrastructure\Persistence\Doctrine\Dbal;

use Doctrine\DBAL\Connection;
use Symfony\Component\Uid\Uuid;
use Tulia\Cms\Website\Domain\WriteModel\Service\TranslationCopyMachine\CopyBusInterface;

/**
 * @author Adam Banaszkiewicz
 * @final
 * @lazy
 */
class DbalWidgetTranslationCopyBus implements CopyBusInterface
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    public function count(string $websiteId, string $defaultLocale): int
    {
        return (int) $this->connection->fetchOne('
            SELECT COUNT(wt.id)
            FROM #__widget_translation AS wt
            INNER JOIN #__widget AS w
                ON w.id = wt.widget_id
            WHERE w.website_id = :website AND wt.locale = :locale', [
            'website' => Uuid::fromString($websiteId)->toBinary(),
            'locale' => $defaultLocale,
        ]);
    }

    public function copy(string $websiteId, string $defaultLocale, string $targetLocale, int $offset, int $limit): int
    {
        $idList = $this->getWidgetIdListPart($websiteId, $offset, $limit);
        $source = $this->getTranslationsSource($idList, $defaultLocale);

        $attributes = $this->collectAttributes(array_column($source, 'id'));

        foreach ($source as $item) {
            $id = Uuid::v4()->toBinary();

            $this->connection->insert('#__widget_translation', [
                'id' => $id,
                'locale' => $targetLocale,
                'widget_id' => $item['widget_id'],
                'title' => $item['title'],
                'visibility' => $item['visibility'],
                'translated' => 0,
            ]);

            foreach ($attributes[$item['id']] ?? [] as $attribute) {
                $this->connection->insert('#__widget_attribute', [
                    'id' => Uuid::v4()->toBinary(),
                    'widget_translation_id' => $id,
                    'locale' => $targetLocale,
                    'code' => $attribute['code'],
                    'uri' => $attribute['uri'],
                    'value' => $attribute['value'],
                    'compiled_value' => $attribute['compiled_value'],
                    'payload' => $attribute['payload'],
                    'flags' => $attribute['flags'],
                ]);
            }
        }

        return count($source);
    }

    public function delete(string $websiteId, string $defaultLocale, string $targetLocale, int $offset, int $limit): int
    {
        $idList = $this->getWidgetIdListPart($websiteId, $offset, $limit);
        $translationsIdList = array_column($this->getTranslationsSource($idList, $targetLocale), 'id');

        $this->connection->executeStatement(
            'DELETE FROM #__widget_attribute WHERE widget_translation_id IN (:id)',
            ['id' => $translationsIdList],
            ['id' => Connection::PARAM_STR_ARRAY]
        );
        $this->connection->executeStatement(
            'DELETE FROM #__widget_translation WHERE id IN (:id)',
            ['id' => $translationsIdList],
            ['id' => Connection::PARAM_STR_ARRAY]
        );

        return count($translationsIdList);
    }

    private function collectAttributes(array $idList): array
    {
        $source = $this->connection->fetchAllAssociative('
            SELECT widget_translation_id, code, uri, value, compiled_value, payload, flags
            FROM #__widget_attribute
            WHERE widget_translation_id IN (:idList)
        ', [
            'idList' => $idList,
        ], [
            'idList' => Connection::PARAM_STR_ARRAY,
        ]);
        $result = [];

        foreach ($source as $item) {
            $result[$item['widget_translation_id']][] = $item;
        }

        return $result;
    }

    private function getWidgetIdListPart(string $websiteId, int $offset, int $limit): array
    {
        return $this->connection->fetchFirstColumn('
            SELECT id
            FROM #__widget
            WHERE website_id = :website
            ORDER BY id ASC
            LIMIT :offset, :limit', [
            'website' => Uuid::fromString($websiteId)->toBinary(),
            'offset' => $offset,
            'limit' => $limit,
        ], [
            'offset' => \PDO::PARAM_INT,
            'limit' => \PDO::PARAM_INT,
        ]);
    }

    private function getTranslationsSource(array $nodeIdList, string $locale): array
    {
        return $this->connection->fetchAllAssociative('
            SELECT id, widget_id, locale, title, visibility
            FROM #__widget_translation
            WHERE widget_id IN (:ids) AND locale = :locale', [
            'locale' => $locale,
            'ids' => $nodeIdList,
        ], [
            'ids' => Connection::PARAM_STR_ARRAY,
        ]);
    }
}
