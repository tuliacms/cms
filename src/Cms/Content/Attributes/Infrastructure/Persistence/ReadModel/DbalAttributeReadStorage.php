<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Attributes\Infrastructure\Persistence\ReadModel;

use Tulia\Cms\Content\Attributes\Domain\ReadModel\Service\AttributeReadStorageInterface;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalAttributeReadStorage implements AttributeReadStorageInterface
{
    protected ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function findAll(string $type, array $ownerId, string $locale): array
    {
        $sql = "SELECT
            tm.owner_id,
            tm.uri,
            tm.name,
            tm.is_renderable,
            tm.has_nonscalar_value,
            COALESCE(tl.value, tm.value) AS `value`,
            COALESCE(tl.compiled_value, tm.compiled_value) AS `compiled_value`,
            COALESCE(tl.payload, tm.payload) AS `payload`
        FROM #__{$type}_attribute AS tm
        LEFT JOIN #__{$type}_attribute_lang AS tl
            ON tm.id = tl.attribute_id AND tl.locale = :locale
        WHERE
            tm.owner_id IN (:owner_id)";

        $source = $this->connection->fetchAllAssociative($sql, [
            'locale' => $locale,
            'owner_id' => $ownerId,
        ], [
            'owner_id' => ConnectionInterface::PARAM_ARRAY_STR,
        ]);

        $result = [];

        foreach ($source as $row) {
            if ($row['has_nonscalar_value']) {
                try {
                    $row['value'] = (array) unserialize(
                        (string) $row['value'],
                        ['allowed_classes' => []]
                    );
                } catch (\ErrorException $e) {
                    // If error, than empty or cannot be unserialized from singular value
                }
            }

            $result[$row['owner_id']][$row['uri']] = [
                'value' => $row['value'],
                'compiled_value' => $row['compiled_value'],
                'payload' => $row['payload'] ? json_decode($row['payload'], true, JSON_THROW_ON_ERROR) : [],
                'uri' => $row['uri'],
                'name' => $row['name'],
                'is_renderable' => $row['is_renderable'],
                'has_nonscalar_value' => $row['has_nonscalar_value'],
            ];
        }

        return $result;
    }
}
