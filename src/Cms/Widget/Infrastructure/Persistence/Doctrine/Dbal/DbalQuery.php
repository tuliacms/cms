<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Infrastructure\Persistence\Doctrine\Dbal;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Exception;
use PDO;
use Symfony\Component\Uid\Uuid;
use Tulia\Cms\Content\Attributes\Domain\ReadModel\Service\LazyAttributesFinder;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\Exception\QueryException;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\Model\Collection;
use Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\Query\AbstractDbalQuery;
use Tulia\Cms\Widget\Domain\ReadModel\Model\Widget;

/**
 * @author Adam Banaszkiewicz
 */
class DbalQuery extends AbstractDbalQuery
{
    public function __construct(
        private readonly QueryBuilder $queryBuilder,
        private readonly DbalWidgetAttributesFinder $attributesFinder,
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
             * @param null|string|array|string[]
             */
            'id__not_in' => null,
            /**
             * @param null|int|bool
             */
            'visibility' => null,
            /**
             * @param null|string|array|string[]
             */
            'space' => null,
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
            'order_by' => 'name',
            'order_dir' => 'DESC',
            /**
             * If query have to count rows, please provide the column name
             * which should be counted. If column to count does not matter,
             * provide boolean `true` and Query does care about column name.
             */
            'count' => null,
            /**
             * Locale of the node to fetch.
             */
            'locale' => null,
            /**
             * Search for rows in the website. Given null search in all websites.
             *
             * @param null|string
             */
            'website_id' => null,
            /**
             * Search widgets by names and titles.
             *
             * @param null|string
             */
            'search' => null,
        ];
    }

    public function query(array $criteria, string $scope): Collection
    {
        $criteria = array_merge($this->getBaseQueryArray(), $criteria);
        $criteria = $this->filterCriteria($criteria);

        $this->searchById($criteria);
        $this->searchByName($criteria);
        $this->setDefaults($criteria);
        $this->buildSpace($criteria);
        $this->buildVisibility($criteria);
        $this->buildOffset($criteria);
        $this->buildOrderBy($criteria);

        $this->callPlugins($criteria);

        return $this->createCollection($this->queryBuilder->execute()->fetchAllAssociative());
    }

    protected function createCollection(array $result): Collection
    {
        $collection = new Collection();

        if ($result === []) {
            return $collection;
        }

        try {
            foreach ($result as $row) {
                $row['styles'] = json_decode($row['styles'], true);
                $row['lazy_attributes'] = new LazyAttributesFinder($row['id'], $row['locale'], $this->attributesFinder);

                $collection->append(Widget::buildFromArray($row));
            }
        } catch (Exception $e) {
            throw new QueryException('Exception during create colection of found nodes: ' . $e->getMessage(), 0, $e);
        }

        return $collection;
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
                tm.type,
                BIN_TO_UUID(tm.id) AS id,
                BIN_TO_UUID(tm.website_id) AS website_id,
                tl.locale,
                tl.translated,
                tl.title,
                tl.visibility
            ');
        }

        if (!$criteria['locale']) {
            throw new \InvalidArgumentException('Please provide "locale" in query parameters.');
        }
        if (!$criteria['website_id']) {
            throw new \InvalidArgumentException('Please provide "website_id" in query parameters.');
        }

        $this->queryBuilder
            ->from('#__widget', 'tm')
            ->leftJoin('tm', '#__widget_translation', 'tl', 'tm.id = tl.widget_id AND tl.locale = :tl_locale')
            ->andWhere('tm.website_id = :tm_website_id')
            ->setParameter('tm_website_id', Uuid::fromString($criteria['website_id'])->toBinary(), PDO::PARAM_STR)
            ->setParameter('tl_locale', $criteria['locale'], PDO::PARAM_STR);
    }

    protected function searchByName(array $criteria): void
    {
        if (! $criteria['search']) {
            return;
        }

        $this->queryBuilder
            ->andWhere('tm.name LIKE :tm_name OR tl.title = :tm_name')
            ->setParameter('tm_name', '%' . $criteria['search'] . '%', PDO::PARAM_STR);
    }

    protected function searchById(array $criteria): void
    {
        if ($criteria['id']) {
            $this->queryBuilder
                ->andWhere('tm.id = :tm_id')
                ->setParameter('tm_id', Uuid::fromString($criteria['id'])->toBinary(), PDO::PARAM_STR)
                ->setMaxResults(1);
        }

        if ($criteria['id__not_in']) {
            if (\is_array($criteria['id__not_in']) === false) {
                $ids = [ $criteria['id__not_in'] ];
            } else {
                $ids = $criteria['id__not_in'];
            }

            $ids = array_map(static fn($v) => Uuid::fromString($v)->toBinary(), $ids);

            $this->queryBuilder
                ->andWhere('tm.id NOT IN (:tm_id__not_in)')
                ->setParameter('tm_id__not_in', $ids, Connection::PARAM_STR_ARRAY);
        }
    }

    protected function buildSpace(array $criteria): void
    {
        if (! $criteria['space']) {
            return;
        }

        if (\is_array($criteria['space']) === false) {
            $criteria['space'] = [$criteria['space']];
        }

        $this->queryBuilder
            ->andWhere('tm.space IN(:tm_spaces)')
            ->setParameter('tm_spaces', $criteria['space'], Connection::PARAM_STR_ARRAY);
    }

    protected function buildVisibility(array $criteria): void
    {
        if (! $criteria['visibility']) {
            return;
        }

        $this->queryBuilder
            ->andWhere('tl.visibility = :tl_visibility')
            ->setParameter('tl_visibility', $criteria['visibility'], PDO::PARAM_INT);
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
        if ($criteria['order_by']) {
            $this->queryBuilder->addOrderBy($criteria['order_by'], $criteria['order_dir']);
        }
    }
}
