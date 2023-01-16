<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Persistence\Doctrine\Dbal\ReadModel;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Exception;
use PDO;
use Symfony\Component\Uid\Uuid;
use Tulia\Cms\Content\Attributes\Domain\ReadModel\Service\LazyAttributesFinder;
use Tulia\Cms\Node\Domain\ReadModel\Model\Node;
use Tulia\Cms\Node\Domain\WriteModel\Model\Term;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\Exception\QueryException;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\Model\Collection;
use Tulia\Cms\Shared\Domain\WriteModel\Model\ValueObject\ImmutableDateTime;
use Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\Query\AbstractDbalQuery;

/**
 * @author Adam Banaszkiewicz
 */
class DbalFinderQuery extends AbstractDbalQuery
{
    protected array $joinedTables = [];

    public function __construct(
        private readonly QueryBuilder $queryBuilder,
        private readonly DbalNodeAttributesFinder $attributesFinder,
        private readonly NodeFinderCache $cache,
    ) {
    }

    public function getBaseQueryArray(): array
    {
        return [
            /**
             * Search for node with given ID.
             * If provided, Query searches only for ONE record (LIMIT 1).
             *
             * @param null|string
             */
            'id' => null,
            /**
             * Search for nodes that are not with provided IDs list.
             *
             * @param null|string|array
             */
            'id__not_in' => null,
            /**
             * Search for children of given parentd IDs. Searches only for the next
             * level - never infinite deep!
             *
             * @param null|string|array
             */
            'children_of' => null,
            /**
             * Search for node with given slug.
             * If provided, Query searches only for ONE record (LIMIT 1).
             *
             * @param null|string
             */
            'slug' => null,
            /**
             * List of node types to search.
             * If not provided, search for all types.
             *
             * @param null|string|array
             */
            'node_type' => [],
            /**
             * If node_type is empty, and node_type__not is provided, query returns all
             * nodes that are not any of given type.
             *
             * @param null|string|array
             */
            'node_type__not' => [],
            /**
             * List of node statuses to search.
             * If not provided, search for all statuses.
             *
             * @param null|string|array
             */
            'node_status' => ['published'],
            /**
             * If node_status is empty, and node_status__not is provided, query returns all
             * nodes that are not any of given statuses.
             *
             * @param null|string|array
             */
            'node_status__not' => [],
            'taxonomy' => [],
            /**
             * Search for nodes in specified category or categories.
             * @param null|string|array
             */
            'term' =>  null,
            /**
             * @param null|int
             */
            'per_page' => null,
            /**
             * @param null|int
             */
            'page' => null,
            /**
             * This field has higher priority than order_by and order_dir.
             * Allows to define custom sort option.
             */
            /*'order' => null,*/
            'order_by' => 'published_at',
            'order_dir' => 'DESC',
            /**
             * In case the node_type supports `hierarchical` and set this value to
             * true, ordering use `level` column to sort nodes like tree.
             */
            'order_hierarchical' => false,
            /**
             * If query have to count rows, please provide the column name
             * which should be counted. If column to count does not matter,
             * provide boolean `true` and Query does care about column name.
             */
            'count' => null,
            /**
             * Search string. Seaching by title with LIKE operator.
             */
            'search' => null,
            /**
             * Locale of the node to fetch.
             */
            'locale' => null,
            'website_id' => null,
            'limit' => null,
            /**
             * Posts with defined purpose or purposes.
             * @param null|string|array
             */
            'purpose' => null,
        ];
    }

    public function query(array $criteria, string $scope): Collection
    {
        $criteria = array_merge($this->getBaseQueryArray(), $criteria);
        $criteria = $this->filterCriteria($criteria);

        $this->searchById($criteria);
        $this->searchBySlug($criteria);
        $this->searchByTitle($criteria);
        $this->setDefaults($criteria);
        $this->buildTerm($criteria);
        $this->buildTaxonomy($criteria);
        $this->buildNodeType($criteria);
        $this->buildNodeStatus($criteria);
        $this->buildNodePurpose($criteria);
        $this->buildDate($criteria);
        $this->buildOffset($criteria);
        $this->buildOrderBy($criteria);

        if ($criteria['limit']) {
            $this->queryBuilder->setMaxResults($criteria['limit']);
        }

        $this->callPlugins($criteria);

        return $this->createCollection($scope, $criteria);
    }

    protected function createCollection(string $scope, array $criteria): Collection
    {
        $result = $this->queryBuilder->fetchAllAssociative();
        $collection = new Collection([], function () {
            $qb = (clone $this->queryBuilder)
                ->select('COUNT(tm.id) AS count')
                ->resetQueryPart('orderBy')
                ->setFirstResult(0)
                ->setMaxResults(null);

            return (int) $qb->getConnection()->fetchOne(
                "SELECT COUNT(*) AS count FROM ({$qb->getSQL()}) AS count_table",
                $qb->getParameters(),
                $qb->getParameterTypes()
            );
        });

        if ($result === []) {
            return $collection;
        }

        $nodesId = array_column($result, 'id');
        $terms = $this->fetchTerms($nodesId);
        $purposes = $this->fetchPurposes($nodesId);

        try {
            foreach ($result as $row) {
                if (isset($terms[$row['id']][Term::TYPE_MAIN][0])) {
                    $row['main_category'] = $terms[$row['id']][Term::TYPE_MAIN][0];
                }
                if (isset($terms[$row['id']][Term::TYPE_ADDITIONAL])) {
                    $row['additional_categories'] = $terms[$row['id']][Term::TYPE_ADDITIONAL];
                }

                $row['lazy_attributes'] = new LazyAttributesFinder($row['id'], $row['locale'], $this->attributesFinder);
                $row['purposes'] = $purposes[$row['id']] ?? [];

                $collection->append(Node::buildFromArray($row));
            }
        } catch (Exception $e) {
            throw new QueryException('Exception during create colection of found nodes: ' . $e->getMessage(), 0, $e);
        }

        return $collection;
    }

    private function fetchPurposes(array $nodeIdList): array
    {
        $source = $this->queryBuilder->getConnection()->fetchAllAssociative('
            SELECT purpose, BIN_TO_UUID(node_id) AS node_id
            FROM #__node_has_purpose
            WHERE node_id IN (:node_id)', [
            'node_id' => array_map(static fn(string $v) => Uuid::fromString($v)->toBinary(), $nodeIdList),
        ], [
            'node_id' => Connection::PARAM_STR_ARRAY,
        ]);
        $result = [];

        foreach ($source as $row) {
            $result[$row['node_id']][] = $row['purpose'];
        }

        return $result;
    }

    private function fetchTerms(array $nodeIdList): array
    {
        return $this->cache->fetchTermsUntilExists($nodeIdList, function ($nodeIdList) {
            $source = $this->queryBuilder->getConnection()->fetchAllAssociative('
                SELECT
                    type,
                    taxonomy,
                    BIN_TO_UUID(node_id) AS node_id,
                    BIN_TO_UUID(term) AS term
                FROM #__node_in_term
                WHERE node_id IN (:node_id)', [
                'node_id' => array_map(static fn(string $v) => Uuid::fromString($v)->toBinary(), $nodeIdList),
            ], [
                'node_id' => Connection::PARAM_STR_ARRAY,
            ]);
            $result = [];

            foreach ($source as $row) {
                $result[$row['node_id']][$row['type']][] = $row['term'];
            }

            return $result;
        });
    }

    protected function setDefaults(array $criteria): void
    {
        if ($criteria['count'] === true) {
            $this->queryBuilder->select('COUNT(tm.id) AS count');
        } elseif ($criteria['count']) {
            $this->queryBuilder->select('COUNT(' . $criteria['count'] . ') AS count');
        } else {
            $this->queryBuilder->select('
                tm.*,
                BIN_TO_UUID(tm.id) AS id,
                BIN_TO_UUID(tm.author) AS author,
                tl.title,
                tl.slug,
                COALESCE(tl.locale, :tl_locale) AS locale
            ');
        }

        if (!$criteria['locale']) {
            throw new \InvalidArgumentException('Please provide "locale" in query parameters.');
        }
        if (!$criteria['website_id']) {
            throw new \InvalidArgumentException('Please provide "website_id" in query parameters.');
        }

        $this->queryBuilder
            ->from('#__node', 'tm')
            ->innerJoin('tm', '#__node_translation', 'tl', 'tm.id = tl.node_id AND tm.website_id = :tm_website_id AND tl.locale = :tl_locale')
            ->setParameter('tl_locale', $criteria['locale'], PDO::PARAM_STR)
            ->setParameter('tm_website_id', Uuid::fromString($criteria['website_id'])->toBinary(), PDO::PARAM_STR)
            ->addGroupBy('tm.id')
        ;
    }

    protected function searchById(array $criteria): void
    {
        if ($criteria['id']) {
            $this->queryBuilder
                ->andWhere('tm.id = :tm_id')
                ->setParameter('tm_id', Uuid::fromString($criteria['id'])->toBinary(), PDO::PARAM_STR)
                ->setMaxResults(1);
        }

        if ($criteria['children_of']) {
            if (\is_array($criteria['children_of']) === false) {
                $ids = [ $criteria['children_of'] ];
            } else {
                $ids = $criteria['children_of'];
            }

            $ids = array_map(static fn(string $v) => Uuid::fromString($criteria['id'])->toBinary(), $ids);

            $this->queryBuilder
                ->andWhere('tm.parent_id IN (:tm_children_of)')
                ->setParameter('tm_children_of', $ids, Connection::PARAM_STR_ARRAY);
        }

        if ($criteria['id__not_in']) {
            if (\is_array($criteria['id__not_in']) === false) {
                $ids = [ $criteria['id__not_in'] ];
            } else {
                $ids = $criteria['id__not_in'];
            }

            $ids = array_map(static fn(string $v) => Uuid::fromString($criteria['id'])->toBinary(), $ids);

            $this->queryBuilder
                ->andWhere('tm.id NOT IN (:tm_id__not_in)')
                ->setParameter('tm_id__not_in', $ids, Connection::PARAM_STR_ARRAY);
        }
    }

    protected function searchBySlug(array $criteria): void
    {
        if (! $criteria['slug']) {
            return;
        }

        $this->queryBuilder
            ->andWhere('tl.slug = :tl_slug')
            ->setParameter('tl_slug', $criteria['slug'], PDO::PARAM_STR)
            ->setMaxResults(1);
    }

    protected function searchByTitle(array $criteria): void
    {
        if (! $criteria['search']) {
            return;
        }

        $this->queryBuilder
            ->andWhere('tl.title LIKE :tl_title')
            ->setParameter('tl_title', '%' . $criteria['search'] . '%', PDO::PARAM_STR);
    }

    protected function buildTerm(array $criteria): void
    {
        if (empty($criteria['term'])) {
            return;
        }

        if (\is_array($criteria['term']) === false) {
            $criteria['term'] = [$criteria['term']];
        }

        $criteria['term'] = array_map(static fn ($v) => Uuid::fromString($v)->toBinary(), $criteria['term']);

        $this->joinTable('node_in_term');

        $this->queryBuilder
            ->andWhere('ttr.term IN (:ttr_term)')
            ->setParameter('ttr_term', $criteria['term'], Connection::PARAM_STR_ARRAY);
    }

    protected function buildTaxonomy(array $criteria): void
    {
        if ($criteria['taxonomy'] === []) {
            return;
        }

        $taxonomy = $criteria['taxonomy'];
        $relation = $taxonomy['relation'] ?? 'AND';

        unset($taxonomy['relation']);

        $this->queryBuilder
            ->innerJoin('tm', '#__node_term_relationship', 'ttr', 'ttr.node_id = tm.id')
            ->innerJoin('ttr', '#__term', 'tt', 'tt.id = ttr.term_id');

        $wheres = [];

        foreach ($taxonomy as $key => $tax) {
            $tax = array_merge([
                'taxonomy' => null,
                'field'    => 'term_id',
                'terms'    => [],
            ], $tax);

            if (! $tax['taxonomy']) {
                continue;
            }

            $field = 'tt.id';

            if (\is_array($tax['terms'])) {
                $wheres[] = "{$tax['field']} IN (:{$tax['field']}_{$key})";
                $this->queryBuilder->setParameter($tax['field'] . '_' . $key, $tax['terms'], Connection::PARAM_STR_ARRAY);
            } else {
                $wheres[] = "{$field} = :{$tax['field']}_{$key}";
                $this->queryBuilder->setParameter($tax['field'] . '_' . $key, $tax['terms'], PDO::PARAM_STR);
            }

            $wheres[] = 'tt.type = :tt_type_' . $key;
            $this->queryBuilder->setParameter('tt_type_' . $key, $tax['taxonomy'], PDO::PARAM_STR);
        }

        $this->queryBuilder->andWhere('(' . implode(" $relation ", $wheres) . ')');
    }

    protected function buildNodeType(array $criteria): void
    {
        $types    = \is_array($criteria['node_type'])      ? $criteria['node_type']      : [ $criteria['node_type'] ];
        $typesNot = \is_array($criteria['node_type__not']) ? $criteria['node_type__not'] : [ $criteria['node_type__not'] ];

        if ($criteria['node_type'] !== null && $criteria['node_type'] !== 'any' && $types !== []) {
            $this->queryBuilder
                ->andWhere('tm.type IN (:tm_type_in)')
                ->setParameter('tm_type_in', $types, Connection::PARAM_STR_ARRAY);
        }

        if ($criteria['node_type__not'] !== null && $typesNot !== []) {
            $this->queryBuilder
                ->andWhere('tm.type NOT IN (:tm_type_not_in)')
                ->setParameter('tm_type_not_in', $typesNot, Connection::PARAM_STR_ARRAY);
        }
    }

    protected function buildNodeStatus(array $criteria): void
    {
        $statuses    = \is_array($criteria['node_status'])      ? $criteria['node_status']      : [ $criteria['node_status'] ];
        $statusesNot = \is_array($criteria['node_status__not']) ? $criteria['node_status__not'] : [ $criteria['node_status__not'] ];

        if ($criteria['node_status'] !== null && $criteria['node_status'] !== 'any' && $statuses !== []) {
            $this->queryBuilder
                ->andWhere('tm.status IN (:tm_status_in)')
                ->setParameter('tm_status_in', $statuses, Connection::PARAM_STR_ARRAY);
        }

        if ($criteria['node_status__not'] !== null && $statusesNot !== []) {
            $this->queryBuilder
                ->andWhere('tm.status NOT IN (:tm_status_not_in)')
                ->setParameter('tm_status_not_in', $statusesNot, Connection::PARAM_STR_ARRAY);
        }
    }

    protected function buildNodePurpose(array $criteria): void
    {
        if (! $criteria['purpose']) {
            return;
        }

        if (is_array($criteria['purpose']) === false) {
            $criteria['purpose'] = [$criteria['purpose']];
        }

        $this->queryBuilder
            ->innerJoin('tm', '#__node_has_purpose', 'nhf', 'tm.id = nhf.node_id AND nhf.purpose IN (:nhf_purpose)')
            ->setParameter('nhf_purpose', $criteria['purpose'], Connection::PARAM_STR_ARRAY)
        ;
    }

    protected function buildDate(array $criteria): void
    {
        $this->queryBuilder
            ->andWhere('IF(
                tm.published_to IS NULL,
                :now >= tm.published_at,
                :now BETWEEN tm.published_at AND tm.published_to
            )')
            ->setParameter('now', ImmutableDateTime::now()->toStringWithPrecision() , PDO::PARAM_STR)
        ;
    }

    protected function buildOffset(array $criteria): void
    {
        if ($criteria['per_page'] && $criteria['page']) {
            $this->queryBuilder->setFirstResult($criteria['page'] <= 1 ? 0 : ($criteria['per_page'] * ($criteria['page'] - 1)));
        }

        if ($criteria['per_page']) {
            $this->queryBuilder->setMaxResults($criteria['per_page']);
        }
    }

    protected function buildOrderBy(array $criteria): void
    {
        if ($criteria['order_hierarchical']) {
            $this->queryBuilder->addOrderBy('tm.level', 'ASC');
        }

        if ($criteria['order_by']) {
            $this->queryBuilder->addOrderBy($criteria['order_by'], $criteria['order_dir']);
        }
    }

    protected function joinTable(string $table): void
    {
        if (isset($this->joinedTables[$table])) {
            return;
        }

        switch ($table) {
            case 'node_in_term':
                $this->queryBuilder->innerJoin('tm', '#__node_in_term', 'ttr', 'ttr.node_id = tm.id');
                $this->joinedTables[$table] = true;
                break;
        }
    }
}
