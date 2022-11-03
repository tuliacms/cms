<?php

declare(strict_types=1);

namespace Tulia\Cms\SearchAnything\Infrastructure\Persistence\Doctrine\Dbal;

use Doctrine\DBAL\Connection;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\SearchAnything\Domain\ReadModel\Query\SearchEngineInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class DbalSearchEngine implements SearchEngineInterface
{
    public function __construct(
        private readonly Connection $connection,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function search(
        string $query,
        string $websiteId,
        string $contentLocale,
        string $userLocale,
        int $limit,
        int $offset,
    ): array {
        $result = $this->connection->fetchAllAssociative(
            'SELECT index_group, title, route, route_parameters, description, poster
            FROM #__search_anything_document
            WHERE
                (
                    (multisite_strategy = "global" AND (
                        (localization_strategy = "user" AND locale = :userLocale)
                        OR
                        (localization_strategy = "content" AND locale = :contentLocale)
                        OR
                        (localization_strategy = "unilingual")
                    ))
                    OR
                    (multisite_strategy = "website" AND website_id = :websiteId AND (
                        (localization_strategy = "user" AND locale = :userLocale)
                        OR
                        (localization_strategy = "content" AND locale = :contentLocale)
                        OR
                        (localization_strategy = "unilingual")
                    ))
                )
                AND MATCH (`title_searchable`,`description_searchable`) AGAINST (:query IN NATURAL LANGUAGE MODE)',
            [
                'query' => transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $query),
                'contentLocale' => $contentLocale,
                'userLocale' => $userLocale,
                'websiteId' => Uuid::fromString($websiteId)->toBinary(),
                'limit' => $limit,
                'offset' => $offset,
            ],
            [
                'query' => \PDO::PARAM_STR,
                'contentLocale' => \PDO::PARAM_STR,
                'userLocale' => \PDO::PARAM_STR,
                'websiteId' => \PDO::PARAM_STR,
                'limit' => \PDO::PARAM_INT,
                'offset' => \PDO::PARAM_INT,
            ]
        );

        foreach ($result as $key => $row) {
            $result[$key]['route_parameters'] = json_decode($row['route_parameters'], true);
            $result[$key]['tags'] = [[
                'tag' => $this->translator->trans('indexGroup_'.$row['index_group'], [], 'search_anything'),
                'icon' => 'fas fa-tag'
            ]];
        }

        return $result;
    }
}
