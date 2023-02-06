<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Persistence\Doctrine\Dbal;

use Doctrine\DBAL\Connection;
use Symfony\Component\Uid\Uuid;
use Tulia\Cms\Content\Attributes\Domain\ReadModel\Service\AbstractAttributesFinder;
use Tulia\Cms\Content\Attributes\Domain\ReadModel\Service\ValueRendering\AttributesValueRenderer;
use Tulia\Cms\Content\Attributes\Domain\WriteModel\Service\UriToArrayTransformer;
use Tulia\Cms\Content\Attributes\Infrastructure\Persistence\ReadModel\DbalSourceAttributeTransformableTrait;

/**
 * @author Adam Banaszkiewicz
 */
final class DbalTermAttributesFinder extends AbstractAttributesFinder
{
    use DbalSourceAttributeTransformableTrait;

    public function __construct(
        UriToArrayTransformer $uriToArrayTransformer,
        AttributesValueRenderer $attributesValueRenderer,
        private readonly Connection $connection,
    ) {
        parent::__construct($uriToArrayTransformer, $attributesValueRenderer);
    }

    public function query(string $ownerId, string $locale): array
    {
        $source = $this->connection->fetchAllAssociative('
            SELECT ta.* FROM #__taxonomy_term_translation_attribute AS ta
            INNER JOIN #__taxonomy_term_translation AS tt
                ON tt.id = ta.term_translation_id
            WHERE
                tt.term_id = :term
                AND ta.locale = :locale
            ',
            [
                'term' => Uuid::fromString($ownerId)->toBinary(),
                'locale' => $locale,
            ],
            [
                'term' => \PDO::PARAM_STR,
                'locale' => \PDO::PARAM_STR,
            ]
        );

        return $this->transformSource($source);
    }
}
