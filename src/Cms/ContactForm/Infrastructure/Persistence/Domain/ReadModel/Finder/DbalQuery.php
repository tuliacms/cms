<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Infrastructure\Persistence\Domain\ReadModel\Finder;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Exception;
use PDO;
use Symfony\Component\Uid\Uuid;
use Tulia\Cms\ContactForm\Domain\ReadModel\Model\Field;
use Tulia\Cms\ContactForm\Domain\ReadModel\Model\Form;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\Exception\QueryException;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\Model\Collection;
use Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\Query\AbstractDbalQuery;

/**
 * @author Adam Banaszkiewicz
 */
class DbalQuery extends AbstractDbalQuery
{
    public function __construct(
        private readonly QueryBuilder $queryBuilder,
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
            'order_by' => 'id',
            'order_dir' => 'DESC',
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
            /**
             * Search for rows in the website. Given null search in all websites.
             *
             * @param null|string
             */
            'website_id' => null,
            /**
             * Whether or not to fetch forms fields too.
             */
            'fetch_fields' => false,
            'limit' => 1,
        ];
    }

    public function query(array $criteria, string $scope): Collection
    {
        $criteria = array_merge($this->getBaseQueryArray(), $criteria);
        $criteria = $this->filterCriteria($criteria);

        $this->searchById($criteria);
        $this->searchByName($criteria);
        $this->setDefaults($criteria);
        $this->buildOffset($criteria);
        $this->buildOrderBy($criteria);

        if ($criteria['limit']) {
            $this->queryBuilder->setMaxResults($criteria['limit']);
        }

        $this->callPlugins($criteria);

        return $this->createCollection($this->queryBuilder->execute()->fetchAllAssociative(), $criteria);
    }

    protected function createCollection(array $result, array $criteria): Collection
    {
        $collection = new Collection();

        if ($result === []) {
            return $collection;
        }

        if ($criteria['fetch_fields']) {
            $fields = $this->fetchFields(array_column($result, 'id'), $criteria);
        } else {
            $fields = [];
        }

        try {
            foreach ($result as $row) {
                if (isset($fields[$row['id']])) {
                    foreach ($fields[$row['id']] as $field) {
                        $row['fields'][] = Field::buildFromArray($field);
                    }
                }

                $row['receivers'] = explode(',', $row['receivers']);

                $collection->append(Form::buildFromArray($row));
            }
        } catch (Exception $e) {
            throw new QueryException('Exception during create colection of found forms: ' . $e->getMessage(), 0, $e);
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
            $this->queryBuilder->select([
                'tm.*',
                'BIN_TO_UUID(tm.id) AS id',
                'tm.name',
                'tl.locale',
                'tl.translated',
                'tl.subject',
                'tl.message_template',
                'tl.fields_view',
                'tl.fields_template',
            ]);
        }

        if (!$criteria['locale']) {
            throw new \InvalidArgumentException('Please provide "locale" in query parameters.');
        }
        if (!$criteria['website_id']) {
            throw new \InvalidArgumentException('Please provide "website_id" in query parameters.');
        }

        $this->queryBuilder
            ->from('#__form', 'tm')
            ->leftJoin('tm', '#__form_translation', 'tl', 'tm.id = tl.form_id AND tl.locale = :tl_locale')
            ->andWhere('tm.website_id = :tm_website')
            ->setParameter('tl_locale', $criteria['locale'], PDO::PARAM_STR)
            ->setParameter('tm_website', Uuid::fromString($criteria['website_id'])->toBinary(), PDO::PARAM_STR)
        ;
    }

    protected function searchById(array $criteria): void
    {
        if ($criteria['id'] !== null) {
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

    protected function searchByName(array $criteria): void
    {
        if (! $criteria['search']) {
            return;
        }

        $this->queryBuilder
            ->andWhere('COALESCE(tl.name, tm.name) LIKE :tl_name')
            ->setParameter('tl_name', '%' . $criteria['search'] . '%', PDO::PARAM_STR);
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

    protected function fetchFields(array $ids, array $criteria): array
    {
        $ids = array_map(static fn($v) => Uuid::fromString($v)->toBinary(), $ids);

        $fields = $this->queryBuilder->getConnection()->fetchAllAssociative("
            SELECT
                tm.name,
                tm.type,
                tm.locale,
                tm.options,
                BIN_TO_UUID(ft.form_id) AS form_id
            FROM #__form_field_translation AS tm
            INNER JOIN #__form_translation AS ft
                ON ft.form_id IN (:form_id) AND ft.id = tm.translation_id
            WHERE tm.locale = :locale", [
            'form_id' => $ids,
            'locale' => $criteria['locale'],
        ], [
            'form_id' => Connection::PARAM_STR_ARRAY
        ]);

        $result = [];

        foreach ($fields as $key => $val) {
            $fields[$key]['options'] = @ (array) json_decode($fields[$key]['options'], true);

            $result[$val['form_id']][] = $fields[$key];
        }

        return $result;
    }
}
