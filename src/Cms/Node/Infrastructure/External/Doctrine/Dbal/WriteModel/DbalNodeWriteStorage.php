<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\External\Doctrine\Dbal\WriteModel;

use Tulia\Cms\Node\Domain\WriteModel\Service\NodeWriteStorageInterface;
use Tulia\Cms\Platform\Infrastructure\Persistence\Domain\AbstractLocalizableStorage;
use Tulia\Cms\Shared\Domain\WriteModel\Model\ValueObject\ImmutableDateTime;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalNodeWriteStorage extends AbstractLocalizableStorage implements NodeWriteStorageInterface
{
    public function __construct(
        private ConnectionInterface $connection,
    ) {
    }

    public function find(string $id, string $websiteId, string $locale, string $defaultLocale): array
    {
        if ($defaultLocale !== $locale) {
            $translationColumn = 'IF(ISNULL(tl.title), 0, 1) AS translated';
        } else {
            $translationColumn = '1 AS translated';
        }

        $node = $this->connection->fetchAllAssociative("
            SELECT
                tm.*,
                COALESCE(tl.locale, :locale) AS locale,
                GROUP_CONCAT(tnhf.purpose SEPARATOR ',') AS purposes,
                {$translationColumn}
            FROM #__node AS tm
            LEFT JOIN #__node_lang AS tl
                ON tm.id = tl.node_id AND tl.locale = :locale
            LEFT JOIN #__node_has_purpose AS tnhf
                ON tm.id = tnhf.node_id
            WHERE tm.id = :id AND tm.website_id = :website_id
            GROUP BY tm.id
            LIMIT 1", [
            'id'     => $id,
            'locale' => $locale,
            'website_id' => $websiteId
        ]);

        if ($node === []) {
            return [];
        }

        $node[0]['purposes'] = $this->findPurposes($id);

        return $node[0];
    }

    public function delete(array $node): void
    {
        $this->connection->delete('#__node', ['id' => $node['id']]);
        $this->connection->delete('#__node_lang', ['node_id' => $node['id']]);
        $this->connection->delete('#__node_term_relationship', ['node_id' => $node['id']]);
        $this->connection->delete('#__node_has_purpose', ['node_id' => $node['id']]);
    }

    public function beginTransaction(): void
    {
        $this->connection->beginTransaction();
    }

    public function commit(): void
    {
        $this->connection->commit();
    }

    public function rollback(): void
    {
        $this->connection->rollback();
    }

    protected function insertMainRow(array $data): void
    {
        $mainTable = [];
        $mainTable['id'] = $data['id'];
        $mainTable['type'] = $data['type'];
        $mainTable['website_id'] = $data['website_id'];
        $mainTable['published_at'] = $this->formatDatetime($data['published_at']);
        $mainTable['published_to'] = $this->formatDatetime($data['published_to']);
        $mainTable['created_at'] = $this->formatDatetime($data['created_at']);
        $mainTable['updated_at'] = $this->formatDatetime($data['updated_at']);
        $mainTable['status'] = $data['status'];
        $mainTable['author_id'] = $data['author_id'];
        $mainTable['slug'] = $data['slug'];
        $mainTable['title'] = $data['title'];
        $mainTable['parent_id'] = $data['parent_id'];

        $this->connection->insert('#__node', $mainTable);
        $this->updatePurposes($data['id'], $data['purposes']);
    }

    protected function updateMainRow(array $data, bool $foreignLocale): void
    {
        $mainTable = [];
        $mainTable['id'] = $data['id'];
        $mainTable['type'] = $data['type'];
        $mainTable['website_id'] = $data['website_id'];
        $mainTable['published_at'] = $this->formatDatetime($data['published_at']);
        $mainTable['published_to'] = $this->formatDatetime($data['published_to']);
        $mainTable['created_at'] = $this->formatDatetime($data['created_at']);
        $mainTable['updated_at'] = $this->formatDatetime($data['updated_at']);
        $mainTable['status'] = $data['status'];
        $mainTable['author_id'] = $data['author_id'];
        $mainTable['parent_id'] = $data['parent_id'];

        if ($foreignLocale === false) {
            $mainTable['slug'] = $data['slug'];
            $mainTable['title'] = $data['title'];
        }

        $this->connection->update('#__node', $mainTable, ['id' => $data['id']]);
        $this->updatePurposes($data['id'], $data['purposes']);
    }

    protected function insertLangRow(array $data): void
    {
        $langTable = [];
        $langTable['node_id'] = $data['id'];
        $langTable['locale'] = $data['locale'];
        $langTable['slug'] = $data['slug'];
        $langTable['title'] = $data['title'];

        $this->connection->insert('#__node_lang', $langTable);
    }

    protected function updateLangRow(array $data): void
    {
        $langTable = [];
        $langTable['slug'] = $data['slug'];
        $langTable['title'] = $data['title'];

        $this->connection->update('#__node_lang', $langTable, [
            'node_id' => $data['id'],
            'locale' => $data['locale'],
        ]);
    }

    protected function langExists(array $data): bool
    {
        $result = $this->connection->fetchAllAssociative(
            'SELECT node_id FROM #__node_lang WHERE node_id = :id AND locale = :locale LIMIT 1',
            ['id' => $data['id'], 'locale' => $data['locale']]
        );

        return isset($result[0]['node_id']) && $result[0]['node_id'] === $data['id'];
    }

    private function formatDatetime($date): ?string
    {
        if ($date instanceof ImmutableDateTime) {
            return $date->format('Y-m-d H:i:s');
        }

        if (is_string($date)) {
            return $date;
        }

        return null;
    }

    private function updatePurposes(string $nodeId, array $purposes): void
    {
        $this->connection->delete('#__node_has_purpose', ['node_id' => $nodeId]);

        foreach ($purposes as $purpose) {
            $this->connection->insert('#__node_has_purpose', [
                'node_id' => $nodeId,
                'purpose' => $purpose,
            ]);
        }
    }

    private function findPurposes(string $nodeId): array
    {
        return $this->connection->fetchFirstColumn(
            'SELECT purpose FROM #__node_has_purpose WHERE node_id = :node_id',
            ['node_id' => $nodeId]
        );
    }

    /**
     * @todo Calculate level after NodeUpdated, NodeCreated and NodeDeleted events.
     */
    /*private function calculateLevel(?string $parentId): int
    {
        if (! $parentId) {
            return 0;
        }

        $level = $this->connection->fetchColumn('SELECT `level` FROM #__node WHERE id = :id LIMIT 1', [
            'id' => $parentId,
        ]);

        if ($level === null) {
            return 0;
        }

        return $level + 1;
    }*/
}
