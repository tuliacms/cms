<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Persistence\Doctrine\Dbal;

use Doctrine\DBAL\Connection;
use Symfony\Component\Uid\Uuid;
use Tulia\Cms\Shared\Domain\WriteModel\Service\SlugGeneratorStrategy\SlugExistenceCheckerInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class DbalTermSlugExistenceChecker implements SlugExistenceCheckerInterface
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    public function exists(string $slug, string $elementId, string $websiteId, string $locale): bool
    {
        return (bool) $this->connection->fetchOne('
            SELECT ttt.id FROM #__taxonomy_term_translation AS ttt
            INNER JOIN #__taxonomy_term AS tt
                ON tt.id = ttt.term_id
            INNER JOIN #__taxonomy AS t
                ON t.website_id = :website_id AND t.id = tt.taxonomy_id
            WHERE
                ttt.locale = :locale AND ttt.slug = :slug AND tt.id != :element_id
            LIMIT 1
        ', [
            'element_id' => Uuid::fromString($elementId)->toBinary(),
            'website_id' => Uuid::fromString($websiteId)->toBinary(),
            'locale' => $locale,
            'slug' => $slug,
        ]);
    }
}
