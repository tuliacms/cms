<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Persistence\Doctrine\Dbal\Datatable;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PDO;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Service\Datatable\TermDatatableFinderInterface;
use Tulia\Component\Datatable\Finder\AbstractDatatableFinder;
use Tulia\Component\Datatable\Finder\FinderContext;

/**
 * @author Adam Banaszkiewicz
 */
class DbalTermDatatableFinder extends AbstractDatatableFinder implements TermDatatableFinderInterface
{
    public function __construct(
        Connection $connection,
        private readonly TranslatorInterface $translator,
    ) {
        parent::__construct($connection);
    }

    public function getColumns(FinderContext $context): array
    {
        return [
            'id' => [
                'selector' => 'BIN_TO_UUID(tm.id)',
                'type' => 'uuid',
                'label' => 'ID',
            ],
            'name' => [
                'selector' => 'tl.name',
                'label' => 'title',
                'view' => '@backend/taxonomy/term/parts/datatable/title.tpl',
            ],
            'visibility' => [
                'selector' => 'tl.visibility',
                'label' => 'visibility',
                'value_translation' => [
                    '1' => $this->translator->trans('visible'),
                    '0' => $this->translator->trans('invisible'),
                ],
                'value_class' => [
                    '1' => 'text-success',
                    '0' => 'text-danger',
                ],
            ],
        ];
    }

    public function prepareQueryBuilder(QueryBuilder $queryBuilder, FinderContext $context): QueryBuilder
    {
        $queryBuilder
            ->from('#__taxonomy_term', 'tm')
            ->addSelect('t.type, tm.level, BIN_TO_UUID(tm.parent_id) AS parent_id, tl.translated, tm.is_root')
            ->innerJoin('tm', '#__taxonomy_term_translation', 'tl', 'tm.id = tl.term_id AND tl.locale = :locale')
            ->innerJoin('tm', '#__taxonomy', 't', 't.type = :type AND t.id = tm.taxonomy_id')
            ->setParameter('type', $context['taxonomyType'], PDO::PARAM_STR)
            ->setParameter('locale', $context['website']->getLocale()->getCode(), PDO::PARAM_STR)
            ->addOrderBy('tm.level', 'ASC')
            ->addOrderBy('tm.position', 'ASC')
        ;

        return $queryBuilder;
    }

    public function prepareResult(array $result): array
    {
        if (empty($result)) {
            return $result;
        }

        return $this->sort($result, $this->findRootId($result));
    }

    public function buildActions(FinderContext $context, array $row): array
    {
        return [
            'main' => '@backend/taxonomy/term/parts/datatable/links/edit-link.tpl',
            'delete' => '@backend/taxonomy/term/parts/datatable/links/delete-link.tpl',
        ];
    }

    private function sort(array $items, string $parent, int $level = 1): array
    {
        $result = [];

        foreach ($items as $item) {
            $item['level'] = (int) $item['level'];
            if ($item['level'] === $level && $item['parent_id'] === $parent) {
                $result[] = [$item];
                $result[] = $this->sort($items, $item['id'], $level + 1);
            }
        }

        if ($result === []) {
            return [];
        }

        return array_merge(...$result);
    }

    private function findRootId(array $result): string
    {
        foreach ($result as $row) {
            if ($row['is_root']) {
                return $row['id'];
            }
        }

        throw new \Exception('Cannot find Root item. I think the Root item does not exists in database for this menu.');
    }
}
