<?php

declare(strict_types=1);

namespace Tulia\Cms\Activity\Persistence;

use Doctrine\DBAL\Connection;
use Tulia\Cms\Activity\Model\ActivityRow;
use Tulia\Cms\Activity\Model\ActivityStorageInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalActivityStorage implements ActivityStorageInterface
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function store(ActivityRow $activityRow): void
    {
        if ($this->recordExists($activityRow->getId())) {
            $this->connection->update('#__activity', [
                'message'            => $activityRow->getMessage(),
                'context'            => json_encode($activityRow->getContext()),
                'translation_domain' => $activityRow->getTranslationDomain(),
            ], [
                'id' => $activityRow->getId(),
            ]);
        } else {
            $this->connection->insert('#__activity', [
                'id'                 => $activityRow->getId(),
                'message'            => $activityRow->getMessage(),
                'context'            => json_encode($activityRow->getContext()),
                'translation_domain' => $activityRow->getTranslationDomain(),
                'created_at'         => $activityRow->getCreatedAt(),
            ]);
        }
    }

    public function delete(ActivityRow $activityRow): void
    {
        $this->connection->delete('#__activity', ['id' => $activityRow->getId()]);
    }


    public function findCollection(int $start = 0, int $limit = 10): array
    {
        $source = $this->connection->fetchAllAssociative(
            "SELECT * FROM #__activity ORDER BY created_at DESC LIMIT {$start}, {$limit}",
        );

        $result = [];

        foreach ($source as $row) {
            $row['context'] = json_decode($row['context'], true);
            $result[] = ActivityRow::fromArray($row);
        }

        return $result;
    }


    private function recordExists(string $id): bool
    {
        return $id === $this->connection->fetchFirstColumn('SELECT id FROM #__activity WHERE id = :id LIMIT 1', ['id' => $id]);
    }
}
