<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Infrastructure\Persistence\Doctrine\Dbal;

use Doctrine\DBAL\Connection;
use Symfony\Component\Uid\Uuid;
use Tulia\Cms\Content\Attributes\Domain\ReadModel\Service\AbstractAttributesFinder;
use Tulia\Cms\Content\Attributes\Domain\ReadModel\Service\ValueRendering\AttributesValueRenderer;
use Tulia\Cms\Content\Attributes\Domain\WriteModel\Service\UriToArrayTransformer;
use Tulia\Cms\Content\Attributes\Infrastructure\Persistence\ReadModel\DbalSourceAttributeTransformableTrait;

/**
 * @author Adam Banaszkiewicz
 */
final class DbalWidgetAttributesFinder extends AbstractAttributesFinder
{
    use DbalSourceAttributeTransformableTrait;

    public function __construct(
        UriToArrayTransformer $uriToArrayTransformer,
        AttributesValueRenderer $attributesValueRenderer,
        private Connection $connection
    ) {
        parent::__construct($uriToArrayTransformer, $attributesValueRenderer);
    }

    public function query(string $ownerId, string $locale): array
    {
        $source = $this->connection->fetchAllAssociative('
            SELECT ta.* FROM #__widget_attribute AS ta
            INNER JOIN #__widget_translation AS tt
                ON tt.id = ta.widget_translation_id
            WHERE
                tt.widget_id = :widdet
                AND ta.locale = :locale
            ',
            [
                'widdet' => Uuid::fromString($ownerId)->toBinary(),
                'locale' => $locale,
            ],
            [
                'widdet' => \PDO::PARAM_STR,
                'locale' => \PDO::PARAM_STR,
            ]
        );

        return $this->transformSource($source);
    }
}
