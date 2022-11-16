<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Persistence\Doctrine\Dbal\ReadModel;

use Doctrine\DBAL\Connection;
use Symfony\Component\Uid\Uuid;
use Tulia\Cms\Content\Attributes\Domain\ReadModel\Service\AbstractAttributesFinder;
use Tulia\Cms\Content\Attributes\Domain\ReadModel\Service\ValueRendering\AttributesValueRenderer;
use Tulia\Cms\Content\Attributes\Domain\WriteModel\Service\UriToArrayTransformer;
use Tulia\Cms\Content\Attributes\Infrastructure\Persistence\ReadModel\DbalSourceAttributeTransformableTrait;

/**
 * @author Adam Banaszkiewicz
 */
final class DbalNodeAttributesFinder extends AbstractAttributesFinder
{
    use DbalSourceAttributeTransformableTrait;

    public function __construct(
        UriToArrayTransformer $uriToArrayTransformer,
        AttributesValueRenderer $attributesValueRenderer,
        private readonly Connection $connection,
    ) {
        parent::__construct($uriToArrayTransformer, $attributesValueRenderer);
    }

    protected function query(string $ownerId, string $locale): array
    {
        $source = $this->connection->fetchAllAssociative('
            SELECT ta.* FROM #__node_attribute AS ta
            INNER JOIN #__node_translation AS tt
                ON tt.id = ta.node_translation_id
            WHERE
                tt.node_id = :node
                AND ta.locale = :locale
            ',
            [
                'node' => Uuid::fromString($ownerId)->toBinary(),
                'locale' => $locale,
            ],
            [
                'node' => \PDO::PARAM_STR,
                'locale' => \PDO::PARAM_STR,
            ]
        );

        return $this->transformSource($source);
    }
}
