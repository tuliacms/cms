<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Persistence\Doctrine\Dbal;

use Doctrine\DBAL\Connection;
use Symfony\Component\Uid\Uuid;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Service\TaxonomyTermsQueryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalTaxonomyTermsQuery implements TaxonomyTermsQueryInterface
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    public function collectVisibleTerms(string $websiteId, string $locale): array
    {
        return $this->connection->fetchAllAssociative("
WITH RECURSIVE tree_path (
    id,
    parent_id,
    position,
    level,
    is_root,
    name,
    slug,
    taxonomy_id
) AS (
        SELECT
            id,
            parent_id,
            position,
            level,
            is_root,
            CAST('root' AS CHAR(255)) AS name,
            CAST('' AS CHAR(255)) AS slug,
            taxonomy_id
        FROM #__taxonomy_term
        WHERE
            is_root = 1
    UNION ALL
        SELECT
            tm.id,
            tm.parent_id,
            tm.position,
            tm.level,
            tm.is_root,
            tl.name,
            tl.slug,
            tm.taxonomy_id
        FROM tree_path AS tp
        INNER JOIN #__taxonomy_term AS tm
            ON tp.id = tm.parent_id
        INNER JOIN #__taxonomy_term_translation AS tl
            ON tm.id = tl.term_id AND tl.locale = :locale
        WHERE tl.visibility = 1
)
SELECT
    *,
    BIN_TO_UUID(id) AS id,
    BIN_TO_UUID(parent_id) AS parent_id,
    BIN_TO_UUID(taxonomy_id) AS taxonomy_id
FROM tree_path
ORDER BY position", [
            'locale' => $locale,
            'website_id' => Uuid::fromString($websiteId)->toBinary(),
        ]);
    }
}
