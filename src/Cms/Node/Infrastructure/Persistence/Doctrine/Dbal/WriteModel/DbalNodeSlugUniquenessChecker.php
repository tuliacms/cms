<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Persistence\Doctrine\Dbal\WriteModel;

use Doctrine\DBAL\Connection;
use Symfony\Component\Uid\Uuid;
use Tulia\Cms\Node\Domain\WriteModel\Service\NodeSlugUniquenessInterface;
use Tulia\Cms\Shared\Domain\WriteModel\Service\SlugGeneratorStrategy\SlugExistenceCheckerInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class DbalNodeSlugUniquenessChecker implements NodeSlugUniquenessInterface, SlugExistenceCheckerInterface
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    public function exists(string $slug, string $elementId, string $websiteId, string $locale): bool
    {
        return !$this->isUniqueInLocale($slug, $websiteId, $locale, $elementId);
    }

    public function isUnique(string $slug, string $websiteId, ?string $locale, ?string $notInThisNode = null): bool
    {
        if ($locale) {
            return $this->isUniqueInLocale($slug, $websiteId, $locale, $notInThisNode);
        }

        return $this->isUniqueInDefaultLocale($slug, $websiteId, $notInThisNode);
    }

    private function isUniqueInLocale(string $slug, string $websiteId, string $locale, ?string $notInThisNode = null): bool
    {
        return ! (bool) $this->connection->fetchOne(<<<EOF
SELECT COUNT(tl.id) AS `count`
FROM #__node_translation AS tl
INNER JOIN #__node AS tm
    ON tm.id = tl.node_id
WHERE
    tl.slug = :slug
    AND tl.locale = :locale
    AND tl.node_id != :id
    AND tm.website_id = :websiteId
EOF,
            [
                'slug' => $slug,
                'locale' => $locale,
                'id' => Uuid::fromString($notInThisNode)->toBinary(),
                'websiteId' => Uuid::fromString($websiteId)->toBinary(),
            ],
            [
                'slug' => \PDO::PARAM_STR,
                'locale' => \PDO::PARAM_STR,
                'id' => \PDO::PARAM_STR,
                'websiteId' => \PDO::PARAM_STR,
            ]
        );
    }

    private function isUniqueInDefaultLocale(string $slug, string $websiteId, ?string $notInThisNode = null): bool
    {
        return ! (bool) $this->connection->fetchOne(<<<EOF
SELECT COUNT(id) AS `count`
FROM #__node
WHERE
    slug = :slug
    AND id != :id
    AND website_id = :websiteId
EOF,
            [
                'slug' => $slug,
                'id' => Uuid::fromString($notInThisNode)->toBinary(),
                'websiteId' => Uuid::fromString($websiteId)->toBinary(),
            ],
            [
                'slug' => \PDO::PARAM_STR,
                'id' => \PDO::PARAM_STR,
                'websiteId' => \PDO::PARAM_STR,
            ]
        );
    }
}
