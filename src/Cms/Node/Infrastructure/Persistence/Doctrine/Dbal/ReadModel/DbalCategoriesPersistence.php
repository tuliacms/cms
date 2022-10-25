<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Persistence\Doctrine\Dbal\ReadModel;

use Doctrine\DBAL\Connection;
use Tulia\Cms\Node\Domain\ReadModel\Persistence\CategoriesPersistenceInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalCategoriesPersistence implements CategoriesPersistenceInterface
{
    public function __construct(
        private Connection $connection
    ) {
    }

    public function update(string $nodeId, string $taxonomy, array $categories): void
    {
        $this->connection->beginTransaction();

        try {
            $this->connection->delete('#__node_term_relationship', [
                'node_id'  => $nodeId,
                'taxonomy' => $taxonomy,
            ]);

            foreach ($categories as $termId => $type) {
                $this->connection->insert('#__node_term_relationship', [
                    'node_id'  => $nodeId,
                    'term_id'  => $termId,
                    'taxonomy' => $taxonomy,
                    'type'     => $type,
                ]);
            }

            $this->connection->commit();
        } catch (\Throwable $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }
}
