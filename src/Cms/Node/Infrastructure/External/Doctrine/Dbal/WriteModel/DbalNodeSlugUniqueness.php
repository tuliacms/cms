<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\External\Doctrine\Dbal\WriteModel;

use Doctrine\DBAL\Connection;
use Tulia\Cms\Node\Domain\WriteModel\Service\NodeSlugUniquenessInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class DbalNodeSlugUniqueness implements NodeSlugUniquenessInterface
{
    public function __construct(
        private Connection $connection
    ) {
    }

    public function isUnique(string $website, string $locale, string $slug, ?string $notInThisNode = null): bool
    {
        return ! (bool) $this->connection->fetchOne(<<<EOF
SELECT COUNT(id) AS `count`
FROM #__node AS n
LEFT JOIN #__node_lang AS nl
    ON nl.node_id = n.id
WHERE
    n.website_id = :website
    AND (
        n.slug = :slug
        OR (
            nl.slug = :slug
            AND nl.locale = :locale
        )
    )
    AND n.id != :id
EOF,
            [
                'website' => $website,
                'slug' => $slug,
                'locale' => $locale,
                'id' => $notInThisNode,
            ],
            [
                'website' => \PDO::PARAM_STR,
                'slug' => \PDO::PARAM_STR,
                'locale' => \PDO::PARAM_STR,
                'id' => \PDO::PARAM_STR,
            ]
        );
    }
}
