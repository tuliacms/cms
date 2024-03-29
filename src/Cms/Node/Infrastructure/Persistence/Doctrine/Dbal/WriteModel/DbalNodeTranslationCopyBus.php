<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Persistence\Doctrine\Dbal\WriteModel;

use Doctrine\DBAL\Connection;
use Symfony\Component\Uid\Uuid;
use Tulia\Cms\Website\Domain\WriteModel\Service\TranslationCopyMachine\CopyBusInterface;

/**
 * @author Adam Banaszkiewicz
 * @final
 * @lazy
 */
class DbalNodeTranslationCopyBus implements CopyBusInterface
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    public function count(string $websiteId, string $defaultLocale): int
    {
        return (int) $this->connection->fetchOne('
            SELECT COUNT(nt.id)
            FROM #__node_translation AS nt
            INNER JOIN #__node AS n
                ON n.id = nt.node_id
            WHERE n.website_id = :website AND nt.locale = :locale', [
            'website' => Uuid::fromString($websiteId)->toBinary(),
            'locale' => $defaultLocale,
        ]);
    }

    public function copy(string $websiteId, string $defaultLocale, string $targetLocale, int $offset, int $limit): int
    {
        $idList = $this->getNodeIdListPart($websiteId, $offset, $limit);
        $source = $this->getTranslationsSource($idList, $defaultLocale);

        $attributes = $this->collectAttributes(array_column($source, 'id'));

        foreach ($source as $item) {
            $id = Uuid::v4()->toBinary();

            $this->connection->insert('#__node_translation', [
                'id' => $id,
                'locale' => $targetLocale,
                'node_id' => $item['node_id'],
                'title' => $item['title'],
                'slug' => $item['slug'],
                'translated' => 0,
            ]);

            foreach ($attributes[$item['id']] ?? [] as $attribute) {
                $this->connection->insert('#__node_attribute', [
                    'id' => Uuid::v4()->toBinary(),
                    'node_translation_id' => $id,
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
        $idList = $this->getNodeIdListPart($websiteId, $offset, $limit);
        $translationsIdList = array_column($this->getTranslationsSource($idList, $targetLocale), 'id');

        $this->connection->executeStatement(
            'DELETE FROM #__node_attribute WHERE node_translation_id IN (:id)',
            ['id' => $translationsIdList],
            ['id' => Connection::PARAM_STR_ARRAY]
        );
        $this->connection->executeStatement(
            'DELETE FROM #__node_translation WHERE id IN (:id)',
            ['id' => $translationsIdList],
            ['id' => Connection::PARAM_STR_ARRAY]
        );

        return count($translationsIdList);
    }

    private function collectAttributes(array $idList): array
    {
        $source = $this->connection->fetchAllAssociative('
            SELECT node_translation_id, code, uri, value, compiled_value, payload, flags
            FROM #__node_attribute
            WHERE node_translation_id IN (:idList)
        ', [
            'idList' => $idList,
        ], [
            'idList' => Connection::PARAM_STR_ARRAY,
        ]);
        $result = [];

        foreach ($source as $item) {
            $result[$item['node_translation_id']][] = $item;
        }

        return $result;
    }

    private function getNodeIdListPart(string $websiteId, int $offset, int $limit): array
    {
        return $this->connection->fetchFirstColumn('
            SELECT id
            FROM #__node
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
            SELECT id, node_id, locale, title, slug
            FROM #__node_translation
            WHERE node_id IN (:ids) AND locale = :locale', [
            'locale' => $locale,
            'ids' => $nodeIdList,
        ], [
            'ids' => Connection::PARAM_STR_ARRAY,
        ]);
    }
}
