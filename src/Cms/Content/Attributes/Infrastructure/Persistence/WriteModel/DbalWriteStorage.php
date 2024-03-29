<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Attributes\Infrastructure\Persistence\WriteModel;

use Doctrine\DBAL\Connection;
use Tulia\Cms\Platform\Infrastructure\Persistence\Domain\AbstractLocalizableStorage;

/**
 * @author Adam Banaszkiewicz
 */
class DbalWriteStorage extends AbstractLocalizableStorage
{
    public function __construct(
        private Connection $connection
    ) {
    }

    public function find(string $type, array $ownerIdList, array $attributes, string $locale): array
    {
        $sql = "SELECT
            tm.owner_id,
            tm.name,
            tm.uri,
            COALESCE(tl.value, tm.value) AS `value`,
            COALESCE(tl.compiled_value, tm.compiled_value) AS `compiled_value`,
            COALESCE(tl.payload, tm.payload) AS `payload`
        FROM #__{$type}_attribute AS tm
        LEFT JOIN #__{$type}_attribute_lang AS tl
            ON tm.id = tl.attribute_id AND tl.locale = :locale
        WHERE
            tm.owner_id IN (:owner_id)
            AND tm.name IN (:names)";

        $source = $this->connection->fetchAllAssociative($sql, [
            'locale' => $locale,
            'owner_id' => $ownerIdList,
            'names' => $attributes,
        ], [
            'owner_id' => Connection::PARAM_STR_ARRAY,
            'names' => Connection::PARAM_STR_ARRAY,
        ]);

        $result = [];

        foreach ($source as $row) {
            $result[$row['owner_id']][$row['uri']] = [
                'value' => $row['value'],
                'compiled_value' => $row['compiled_value'],
                'payload' => $row['payload'] ? json_decode($row['payload'], true, JSON_THROW_ON_ERROR) : [],
                'uri' => $row['uri'],
                'name' => $row['name'],
            ];
        }

        return $result;
    }

    public function persist(array $attributes, string $defaultLocale): void
    {
        foreach ($attributes as $item) {
            $mainRow = $this->findMainRow($item);

            if ($mainRow === []) {
                $this->insert($item, $defaultLocale);
            } else {
                $item['id'] = $mainRow['id'];
                $this->update($item, $defaultLocale);
            }
        }
    }

    public function delete(string $type, string $ownerId): void
    {
        $this->connection->executeUpdate("DELETE tm, tl
            FROM #__{$type}_attribute AS tm
            JOIN #__{$type}_attribute_lang AS tl
                ON tm.id = tl.attribute_id
            WHERE
                tm.owner_id = :owner_id", [
            'owner_id' => $ownerId,
        ]);
    }

    protected function insertMainRow(array $data): void
    {
        $this->connection->insert("#__{$data['type']}_attribute", [
            'id' => $data['id'],
            'owner_id' => $data['owner_id'],
            'name' => $data['name'],
            'value' => $data['value'],
            'compiled_value' => $data['compiled_value'],
            'payload' => json_encode($data['payload']),
            'uri' => $data['uri'],
            'is_renderable' => ((bool) $data['is_renderable']) ? 1 : 0,
            'has_nonscalar_value' => ((bool) $data['has_nonscalar_value']) ? 1 : 0,
        ]);
    }

    protected function updateMainRow(array $data, bool $foreignLocale): void
    {
        if ($foreignLocale && $data['is_multilingual'] === true) {
            return;
        }

        $this->connection->update("#__{$data['type']}_attribute", [
            'value' => $data['value'],
            'compiled_value' => $data['compiled_value'],
            'payload' => json_encode($data['payload']),
            'is_renderable' => ((bool) $data['is_renderable']) ? 1 : 0,
            'has_nonscalar_value' => ((bool) $data['has_nonscalar_value']) ? 1 : 0,
        ], [
            'owner_id' => $data['owner_id'],
            'name' => $data['name'],
            'uri' => $data['uri'],
        ]);
    }

    protected function insertLangRow(array $data): void
    {
        if ($data['is_multilingual'] === false) {
            return;
        }

        $this->connection->insert("#__{$data['type']}_attribute_lang", [
            'attribute_id' => $data['id'],
            'value' => $data['value'],
            'compiled_value' => $data['compiled_value'],
            'payload' => json_encode($data['payload']),
            'locale' => $data['locale'],
        ]);
    }

    protected function updateLangRow(array $data): void
    {
        if ($data['is_multilingual'] === false) {
            return;
        }

        $this->connection->update("#__{$data['type']}_attribute_lang", [
            'value' => $data['value'],
            'compiled_value' => $data['compiled_value'],
            'payload' => json_encode($data['payload']),
        ], [
            'attribute_id' => $data['id'],
            'locale' => $data['locale'],
        ]);
    }

    protected function langExists(array $data): bool
    {
        if ($data['is_multilingual'] === false) {
            return false;
        }

        $sql = "SELECT
            tl.attribute_id
        FROM #__{$data['type']}_attribute AS tm
        LEFT JOIN #__{$data['type']}_attribute_lang AS tl
            ON tm.id = tl.attribute_id AND tl.locale = :locale
        WHERE
            tm.owner_id IN (:owner_id) AND tm.`uri` = :uri
        LIMIT 1";

        $result = $this->connection->fetchAllAssociative($sql, [
            'locale' => $data['locale'],
            'owner_id' => $data['owner_id'],
            'uri' => $data['uri'],
        ], [
            'owner_id' => \PDO::PARAM_STR,
        ]);

        return isset($result[0]['attribute_id']) && $result[0]['attribute_id'] === $data['id'];
    }

    protected function findMainRow(array $attribute): array
    {
        $sql = "SELECT id
        FROM #__{$attribute['type']}_attribute
        WHERE owner_id = :owner_id AND `uri` = :uri
        LIMIT 1";

        $result = $this->connection->fetchAllAssociative($sql, [
            'uri' => $attribute['uri'],
            'owner_id' => $attribute['owner_id'],
        ], [
            'uri' => \PDO::PARAM_STR,
            'owner_id' => \PDO::PARAM_STR,
        ]);

        return $result[0] ?? [];
    }
}
