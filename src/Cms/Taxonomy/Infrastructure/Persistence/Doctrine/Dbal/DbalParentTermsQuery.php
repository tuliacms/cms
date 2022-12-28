<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Persistence\Doctrine\Dbal;

use Doctrine\DBAL\Connection;
use Symfony\Component\Uid\Uuid;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Service\ParentTermsQueryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class DbalParentTermsQuery implements ParentTermsQueryInterface
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    public function fetchAllParents(string $term, string $taxonomy, string $websiteId): array
    {
        $terms = $this->connection->fetchAllAssociative('SELECT
            BIN_TO_UUID(tt.id) AS id,
            BIN_TO_UUID(tt.parent_id) AS parent_id
        FROM #__taxonomy_term AS tt
        INNER JOIN #__taxonomy AS t
            ON t.id = tt.taxonomy_id
        WHERE t.type = :type AND t.website_id = :website_id AND tt.is_root = 0', [
            'type' => $taxonomy,
            'website_id' => Uuid::fromString($websiteId)->toBinary(),
        ]);

        foreach ($terms as $child) {
            if ($child['id'] === $term) {
                return $this->findParentsOf($terms, $child['parent_id']);
            }
        }

        return [];
    }

    private function findParentsOf(array $terms, ?string $child): array
    {
        $parents = [];
        $securityLoops = 100;

        while ($child && $securityLoops) {
            $parents[] = $child;

            $found = null;

            foreach ($terms as $pretendend) {
                if ($pretendend['id'] === $child) {
                    $found = $pretendend['parent_id'];
                }
            }

            if (!$found) {
                break;
            }

            $child = $found;
            $securityLoops--;
        }

        return $parents;
    }
}
