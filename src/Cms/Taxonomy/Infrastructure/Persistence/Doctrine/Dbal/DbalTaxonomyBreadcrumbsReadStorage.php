<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Persistence\Doctrine\Dbal;

use Doctrine\DBAL\Connection;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Service\TaxonomyBreadcrumbsReadStorageInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalTaxonomyBreadcrumbsReadStorage implements TaxonomyBreadcrumbsReadStorageInterface
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    public function find(string $termId, string $locale, string $defaultLocale): array
    {
        return $this->connection->fetchAllAssociative("
WITH RECURSIVE tree_path (
    id,
    type,
    is_root,
    parent_id,
    position,
    level,
    count,
    locale,
    title,
    slug,
    visibility,
    generated_path
) AS (
    SELECT
        id,
        type,
        is_root,
        parent_id,
        position,
        level,
        count,
        :defaultLocale AS locale,
        title,
        slug,
        visibility,
        CONCAT(title, '/') as generated_path
    FROM #__term
    WHERE
        id = :term_id
UNION ALL
    SELECT
            tm.id,
            tm.type,
            tm.is_root,
            tm.parent_id,
            tm.position,
            tm.level,
            tm.count,
            COALESCE(tl.locale, :defaultLocale) AS locale,
            COALESCE(tl.title, tm.title) AS title,
            COALESCE(tl.slug, tm.slug) AS slug,
            COALESCE(tl.visibility, tm.visibility) AS visibility,
            CONCAT(tp.generated_path, tm.title, '/') AS generated_path
        FROM tree_path AS tp
        INNER JOIN #__term AS tm
            ON tp.parent_id = tm.id
        LEFT JOIN #__term_lang AS tl
            ON tm.id = tl.term_id AND tl.locale = :locale
)
SELECT * FROM tree_path", [
            'term_id' => $termId,
            'locale' => $locale,
            'defaultLocale' => $defaultLocale,
        ]);
    }
}
