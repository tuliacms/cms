<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Persistence\Doctrine\Dbal;

use Doctrine\DBAL\Connection;
use Symfony\Component\Uid\Uuid;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Service\TaxonomyTermsTreeQueryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class DbalTaxonomyTermsTreeQuery implements TaxonomyTermsTreeQueryInterface
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    public function getTermsTreeFor(string $taxonomy, string $websiteId, string $locale): array
    {
        $terms = $this->connection->fetchAllAssociative('
        SELECT
            BIN_TO_UUID(tt.id) AS id,
            tt.position,
            BIN_TO_UUID(tt.parent_id) AS parent_id,
            tt.is_root,
            ttt.name
        FROM #__taxonomy_term AS tt
        INNER JOIN #__taxonomy AS t
            ON t.type = :taxonomy AND t.website_id = :website_id AND tt.taxonomy_id = t.id
        INNER JOIN #__taxonomy_term_translation AS ttt
            ON ttt.term_id = tt.id AND ttt.locale = :locale
        ORDER BY tt.position
        ', [
            'taxonomy' => $taxonomy,
            'website_id' => Uuid::fromString($websiteId)->toBinary(),
            'locale' => $locale,
        ]);

        $root = null;

        foreach ($terms as $term) {
            if ($term['is_root']) {
                $root = $term['id'];
            }
        }

        return $this->buildTree($root, $terms);
    }

    private function buildTree(?string $parentId, array $terms): array
    {
        $tree = [];

        foreach ($terms as $term) {
            if ($term['parent_id'] && $term['parent_id'] === $parentId) {
                $leaf = [
                    'id' => $term['id'],
                    'name' => $term['name'],
                    'position' => $term['position'],
                    'children' => $this->buildTree($term['id'], $terms),
                ];

                $tree[] = $leaf;
            }
        }

        usort($tree, function (array $a, array $b) {
            return $a['position'] <=> $b['position'];
        });

        return $tree;
    }
}
