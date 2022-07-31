<?php

declare(strict_types=1);

namespace Tulia\Cms\SearchAnything\Infrastructure\Persistence\Doctrine\Dbal;

use Doctrine\DBAL\Connection;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\SearchAnything\Domain\ReadModel\Query\SearchEngineInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class DbalSearchEngine implements SearchEngineInterface
{
    public function __construct(
        private Connection $connection,
        private TranslatorInterface $translator
    ) {
    }

    public function search(string $query, string $locale, int $limit, int $offset): array
    {
        $result = $this->connection->fetchAllAssociative(
            'SELECT index_group, title, route, route_parameters, description, poster
            FROM #__search_anything_document
            WHERE
                locale = :locale
                AND MATCH (`title`,`description`) AGAINST(:query IN NATURAL LANGUAGE MODE)',
            [
                'query' => $query,
                'locale' => $locale,
                'limit' => $limit,
                'offset' => $offset,
            ],
            [
                'query' => \PDO::PARAM_STR,
                'locale' => \PDO::PARAM_STR,
                'limit' => \PDO::PARAM_INT,
                'offset' => \PDO::PARAM_INT,
            ]
        );

        foreach ($result as $key => $row) {
            $result[$key]['route_parameters'] = unserialize($row['route_parameters'], ['allowed_classes' => []]);
            $result[$key]['tags'] = [[
                'tag' => $this->translator->trans('indexGroup_'.$row['index_group'], [], 'search_anything'),
                'icon' => 'fas fa-tag'
            ]];
        }

        return $result;
    }
}
