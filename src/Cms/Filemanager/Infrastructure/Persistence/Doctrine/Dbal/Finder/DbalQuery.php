<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Infrastructure\Persistence\Doctrine\Dbal\Finder;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Exception;
use PDO;
use Symfony\Component\Uid\Uuid;
use Tulia\Cms\Content\Attributes\Domain\ReadModel\Service\AttributesFinder;
use Tulia\Cms\Filemanager\Domain\ReadModel\Model\File;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\Exception\QueryException;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\Model\Collection;
use Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\Query\AbstractDbalQuery;

/**
 * @author Adam Banaszkiewicz
 */
class DbalQuery extends AbstractDbalQuery
{
    protected array $joinedTables = [];

    public function __construct(
        private readonly QueryBuilder $queryBuilder,
        private readonly AttributesFinder $attributesFilder,
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
            'id__in' => null,
            /**
             * Search for nodes that are not with provided IDs list.
             *
             * @param null|string|array
             */
            'id__not_in' => null,
            /**
             * Type of file. `null` means searching for every type.
             *
             * @param null|string|array
             */
            'type' => null,
            /**
             * @param null|int
             */
            'per_page' => null,
            /**
             * @param null|int
             */
            'page' => null,
            'order_by' => 'created_at',
            'order_dir' => 'DESC',
            /**
             * Directory where files should be placed.
             *
             * @param null|string|array
             */
            'directory' => null,
            /**
             * If query have to count rows, please provide the column name
             * which should be counted. If column to count does not matter,
             * provide boolean `true` and Query does care about column name.
             */
            'count' => null,
            /**
             * Locale of the node to fetch.
             */
            'locale' => 'en_US',
            'limit' => null,
        ];
    }

    public function query(array $criteria, string $scope): Collection
    {
        $criteria = array_merge($this->getBaseQueryArray(), $criteria);
        $criteria = $this->filterCriteria($criteria);

        $this->searchById($criteria);
        $this->setDefaults($criteria);
        $this->buildType($criteria);
        $this->buildDirectory($criteria);
        $this->buildOffset($criteria);
        $this->buildOrderBy($criteria);

        if ($criteria['limit']) {
            $this->queryBuilder->setMaxResults($criteria['limit']);
        }

        $this->callPlugins($criteria);

        return $this->createCollection($this->queryBuilder->execute()->fetchAllAssociative());
    }

    protected function createCollection(array $result): Collection
    {
        $collection = new Collection();

        if ($result === []) {
            return $collection;
        }

        //$metadata = $this->metadataFinder->findAllAggregated(NodeMetadataEnum::TYPE, array_column($result, 'id'));

        try {
            foreach ($result as $row) {
                //$row['metadata'] = $metadata[$row['id']] ?? [];

                $collection->append(File::buildFromArray($row));
            }
        } catch (Exception $e) {
            throw new QueryException('Exception during create colection of found files: ' . $e->getMessage(), 0, $e);
        }

        return $collection;
    }

    /*protected function setDefaults(array $criteria): void
    {
        if ($criteria['count'] === true) {
            $this->queryBuilder->select('COUNT(tm.id) AS count');
        } elseif ($criteria['count']) {
            $this->queryBuilder->select('COUNT(' . $criteria['count'] . ') AS count');
        } else {
            $this->queryBuilder->select('
                tm.*,
                COALESCE(tl.title, tm.title) AS title,
                COALESCE(tl.slug, tm.slug) AS slug,
                COALESCE(tl.introduction, tm.introduction) AS introduction,
                COALESCE(tl.content_compiled, tm.content_compiled) AS content,
                COALESCE(tl.locale, :tl_locale) AS locale
            ');
        }

        $this->queryBuilder
            ->from('#__node', 'tm')
            ->leftJoin('tm', '#__node_lang', 'tl', 'tm.id = tl.node_id AND tl.locale = :tl_locale')
            ->setParameter('tl_locale', $criteria['locale'], PDO::PARAM_STR);
    }*/

    protected function setDefaults(array $criteria): void
    {
        if ($criteria['count'] === true) {
            $this->queryBuilder->select('COUNT(tm.id) AS count');
        } elseif ($criteria['count']) {
            $this->queryBuilder->select('COUNT(' . $criteria['count'] . ') AS count');
        } else {
            $this->queryBuilder->select('tm.*, BIN_TO_UUID(id) AS id');
        }

        $this->queryBuilder->from('#__filemanager_file', 'tm');
    }

    protected function buildType(array $criteria): void
    {
        if ($criteria['type']) {
            if (\is_array($criteria['type'])) {
                $this->queryBuilder
                    ->andWhere('tm.type IN (:tm_type)')
                    ->setParameter('tm_type', $criteria['type'], Connection::PARAM_STR_ARRAY);
            } else {
                $this->queryBuilder
                    ->andWhere('tm.type = :tm_type')
                    ->setParameter('tm_type', $criteria['type']);
            }
        }
    }

    protected function buildDirectory(array $criteria): void
    {
        if ($criteria['directory']) {
            if (\is_array($criteria['directory'])) {
                $this->queryBuilder
                    ->andWhere('tm.directory_id IN (:tm_directory)')
                    ->setParameter('tm_directory', $criteria['directory'], Connection::PARAM_STR_ARRAY);
            } else {
                $this->queryBuilder
                    ->andWhere('tm.directory_id = :tm_directory')
                    ->setParameter('tm_directory', Uuid::fromString($criteria['directory'])->toBinary());
            }
        }
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

        if ($criteria['id__in']) {
            if (\is_array($criteria['id__in']) === false) {
                $ids = [ $criteria['id__in'] ];
            } else {
                $ids = $criteria['id__in'];
            }

            $ids = array_map(static fn($v) => Uuid::fromString($v)->toBinary(), $ids);

            $this->queryBuilder
                ->andWhere('tm.id IN (:tm_id__in)')
                ->setParameter('tm_id__in', $ids, Connection::PARAM_STR_ARRAY);
        }
    }

    /**
     * @param array $criteria
     */
    protected function buildOffset(array $criteria): void
    {
        if ($criteria['per_page'] && $criteria['page']) {
            $this->queryBuilder->setFirstResult($criteria['page'] <= 1 ? 0 : ($criteria['per_page'] * ($criteria['page'] - 1)));
        }

        if ($criteria['per_page']) {
            $this->queryBuilder->setMaxResults($criteria['per_page']);
        }
    }

    /**
     * @param array $criteria
     */
    protected function buildOrderBy(array $criteria): void
    {
        if ($criteria['order_by']) {
            if (\is_array($criteria['order_dir'])) {
                $field = $criteria['order_by'];
                $ids = array_map(function ($id) {
                    return "'{$id}'";
                }, $criteria['order_dir']);

                $this->queryBuilder->addOrderBy(sprintf('FIELD(tm.`%s`, %s)', $field, implode(', ', $ids)));
            } else {
                $this->queryBuilder->addOrderBy($criteria['order_by'], $criteria['order_dir']);
            }
        }
    }
}
