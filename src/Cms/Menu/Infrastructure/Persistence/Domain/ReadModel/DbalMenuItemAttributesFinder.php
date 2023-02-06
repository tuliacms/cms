<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Persistence\Domain\ReadModel;

use Doctrine\DBAL\Connection;
use Symfony\Component\Uid\Uuid;
use Tulia\Cms\Content\Attributes\Domain\ReadModel\Service\AbstractAttributesFinder;
use Tulia\Cms\Content\Attributes\Domain\ReadModel\Service\ValueRendering\AttributesValueRenderer;
use Tulia\Cms\Content\Attributes\Domain\WriteModel\Service\UriToArrayTransformer;
use Tulia\Cms\Content\Attributes\Infrastructure\Persistence\ReadModel\DbalSourceAttributeTransformableTrait;

/**
 * @author Adam Banaszkiewicz
 */
final class DbalMenuItemAttributesFinder extends AbstractAttributesFinder
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
            SELECT ta.* FROM #__menu_item_translation_attribute AS ta
            INNER JOIN #__menu_item_translation AS tt
                ON tt.id = ta.item_translation_id
            WHERE
                tt.item_id = :item
                AND ta.locale = :locale
            ',
            [
                'item' => Uuid::fromString($ownerId)->toBinary(),
                'locale' => $locale,
            ],
            [
                'item' => \PDO::PARAM_STR,
                'locale' => \PDO::PARAM_STR,
            ]
        );

        return $this->transformSource($source);
    }
}
