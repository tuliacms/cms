<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\External\Doctrine\Dbal\WriteModel;

use Doctrine\DBAL\Connection;
use Symfony\Component\Uid\Uuid;
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

    public function isUnique(string $slug, ?string $locale, ?string $notInThisNode = null): bool
    {
        if ($locale) {
            return $this->isUniqueInLocale($slug, $locale, $notInThisNode);
        }

        return $this->isUniqueInDefaultLocale($slug, $notInThisNode);
    }

    private function isUniqueInLocale(string $slug, string $locale, ?string $notInThisNode = null): bool
    {
        return ! (bool) $this->connection->fetchOne(<<<EOF
SELECT COUNT(id) AS `count`
FROM #__node_translation
WHERE
    slug = :slug
    AND locale = :locale
    AND node_id != :id
EOF,
            [
                'slug' => $slug,
                'locale' => $locale,
                'id' => Uuid::fromString($notInThisNode)->toBinary(),
            ],
            [
                'slug' => \PDO::PARAM_STR,
                'locale' => \PDO::PARAM_STR,
                'id' => \PDO::PARAM_STR,
            ]
        );
    }

    private function isUniqueInDefaultLocale(string $slug, ?string $notInThisNode = null): bool
    {
        return ! (bool) $this->connection->fetchOne(<<<EOF
SELECT COUNT(id) AS `count`
FROM #__node
WHERE
    slug = :slug
    AND id != :id
EOF,
            [
                'slug' => $slug,
                'id' => Uuid::fromString($notInThisNode)->toBinary(),
            ],
            [
                'slug' => \PDO::PARAM_STR,
                'id' => \PDO::PARAM_STR,
            ]
        );
    }
}
