<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Persistence\Doctrine\Dbal;

use Doctrine\DBAL\Connection;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Service\TaxonomyTermsQueryInterface;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Service\TermsOfTaxonomiesQueryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class DbalTermsOfTaxonomiesQuery implements TermsOfTaxonomiesQueryInterface
{
    public function __construct(
        private readonly Connection $connection,
        private readonly TaxonomyTermsQueryInterface $storage,
        private readonly ContentTypeRegistryInterface $typeRegistry,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function allTermsGrouppedByTaxonomy(string $websiteId, string $locale): array
    {
        $taxonomies = $this->connection->fetchAllAssociative(
            'SELECT BIN_TO_UUID(id) AS id, type FROM #__taxonomy WHERE website_id = :website_id',
            ['website_id' => Uuid::fromString($websiteId)->toBinary()]
        );
        $allTerms = $this->storage->collectVisibleTerms($websiteId, $locale);
        $result = [];

        foreach ($this->typeRegistry->allByType('taxonomy') as $contentType) {
            $taxonomy = null;

            foreach ($taxonomies as $pretendent) {
                if ($pretendent['type'] === $contentType->getCode()) {
                    $taxonomy = $pretendent;
                }
            }

            if (!$taxonomy) {
                continue;
            }

            $terms = [];

            foreach ($allTerms as $term) {
                if ($term['taxonomy_id'] === $taxonomy['id'] && !$term['is_root']) {
                    $terms[] = [
                        'id' => $term['id'],
                        'name' => $term['name'],
                    ];
                }
            }

            $result[] = [
                'id' => $contentType->getCode(),
                'name' => $this->translator->trans($contentType->getName(), [], 'taxonomy'),
                'terms' => $terms,
            ];
        }

        return $result;
    }
}
