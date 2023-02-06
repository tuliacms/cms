<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Persistence\Domain\ReadModel\Finder\Query;

use Doctrine\DBAL\Query\QueryBuilder;
use Exception;
use PDO;
use Symfony\Component\Uid\Uuid;
use Tulia\Cms\Content\Attributes\Domain\ReadModel\Service\AttributesFinder;
use Tulia\Cms\Content\Attributes\Domain\ReadModel\Service\LazyAttributesFinder;
use Tulia\Cms\Menu\Domain\ReadModel\Model\Menu;
use Tulia\Cms\Menu\Infrastructure\Persistence\Domain\ReadModel\DbalMenuItemAttributesFinder;
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
        private readonly DbalMenuItemAttributesFinder $attributesFinder,
    ) {
    }

    public function getBaseQueryArray(): array
    {
        return [
            /**
             * Search for menu with given ID.
             * If provided, Query searches only for ONE record (LIMIT 1).
             * @param null|string
             */
            'id' => null,
            /**
             * Search for rows in the website. Given null search in all websites.
             * @param null|string
             */
            'website_id' => null,
            /**
             * Locale of the menu/menu_items to fetch.
             */
            'locale' => null,
            /**
             * Visibility of menu items.
             * @param null|int|bool
             */
            'visibility' => 1,
            /**
             * Tells whether or not to fetch also menu items.
             * @param bool
             */
            'fetch_items' => true,
            'limit' => null,
            'fetch_root' => false,
        ];
    }

    public function query(array $criteria, string $scope): Collection
    {
        $criteria = array_merge($this->getBaseQueryArray(), $criteria);
        $criteria = $this->filterCriteria($criteria);

        $this->setDefaults($criteria);

        if ($criteria['limit']) {
            $this->queryBuilder->setMaxResults($criteria['limit']);
        }

        $this->callPlugins($criteria);

        return $this->createCollection($this->queryBuilder->execute()->fetchAllAssociative(), $scope, $criteria);
    }

    protected function createCollection(array $result, string $scope, array $criteria): Collection
    {
        $collection = new Collection();

        if ($result === []) {
            return $collection;
        }

        $items = [];

        if ($criteria['fetch_items']) {
            $items = $this->fetchMenuItems($criteria);

            foreach ($items as $key => $row) {
                if (!$criteria['fetch_root'] && $row['is_root']) {
                    unset($items[$key]);
                    continue;
                }

                $items[$key]['lazy_attributes'] = new LazyAttributesFinder($row['id'], $criteria['locale'], $this->attributesFinder);
            }
        }

        try {
            foreach ($result as $row) {
                $row['items'] = $items;

                $collection->append(Menu::buildFromArray($row));
            }
        } catch (Exception $e) {
            throw new QueryException('Exception during create colection of found menus: ' . $e->getMessage(), 0, $e);
        }

        return $collection;
    }

    protected function setDefaults(array $criteria): void
    {
        if (!$criteria['locale']) {
            throw new \InvalidArgumentException('Please provide "locale" in query parameters.');
        }
        if (!$criteria['website_id']) {
            throw new \InvalidArgumentException('Please provide "website_id" in query parameters.');
        }

        $qb = $this->queryBuilder
            ->select('tm.*, BIN_TO_UUID(tm.id) AS id, BIN_TO_UUID(tm.website_id) AS website_id')
            ->from('#__menu', 'tm')
            ->where('tm.website_id = :tm_website_id')
            ->setParameter('tm_website_id', Uuid::fromString($criteria['website_id'])->toBinary(), PDO::PARAM_STR);

        if ($criteria['id']) {
            $qb->andWhere('tm.id = :tm_id')
                ->setParameter('tm_id', Uuid::fromString($criteria['id'])->toBinary(), PDO::PARAM_STR)
                ->setMaxResults(1);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function fetchMenuItems(array $criteria): array
    {
        $where = ['1 = 1'];
        $parameters = [
            'menu_id' => $criteria['id'] ? Uuid::fromString($criteria['id'])->toBinary() : $criteria['id'],
            'locale'  => $criteria['locale'],
        ];

        if ($criteria['visibility']) {
            $where[] = 'tl.visibility = :tl_visibility';
            $parameters['tl_visibility'] = $criteria['visibility'];
        }

        $where = implode(' AND ', $where);

        return $this->queryBuilder->getConnection()->fetchAllAssociative("
WITH RECURSIVE tree_path (
    id,
    menu_id,
    parent_id,
    position,
    level,
    is_root,
    type,
    identity,
    hash,
    target,
    name,
    visibility
) AS (
        SELECT
            id,
            menu_id,
            parent_id,
            position,
            level,
            is_root,
            type,
            identity,
            hash,
            target,
            CAST('root' AS CHAR(255)) AS name,
            1 AS visibility
        FROM #__menu_item
        WHERE
            is_root = 1
            AND menu_id = :menu_id
    UNION ALL
        SELECT
            tm.id,
            tm.menu_id,
            tm.parent_id,
            tm.position,
            tm.level,
            tm.is_root,
            tm.type,
            tm.identity,
            tm.hash,
            tm.target,
            tl.name,
            tl.visibility
        FROM tree_path AS tp
        INNER JOIN #__menu_item AS tm
            ON tp.id = tm.parent_id
        INNER JOIN #__menu_item_translation AS tl
            ON tm.id = tl.item_id AND tl.locale = :locale
        WHERE
            {$where}
)
SELECT
    *,
    BIN_TO_UUID(id) AS id,
    BIN_TO_UUID(menu_id) AS menu_id,
    BIN_TO_UUID(parent_id) AS parent_id
FROM tree_path
ORDER BY position", $parameters);
    }
}
