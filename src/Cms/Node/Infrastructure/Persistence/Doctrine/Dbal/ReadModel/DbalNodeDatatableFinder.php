<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Persistence\Doctrine\Dbal\ReadModel;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PDO;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Content\Attributes\Domain\ReadModel\Service\LazyAttributesFinder;
use Tulia\Cms\Content\Type\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\Node\Domain\ReadModel\Datatable\NodeDatatableFinderInterface;
use Tulia\Cms\Node\Domain\WriteModel\Service\NodeOptionsInterface;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Finder\TermFinderInterface;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Finder\TermFinderScopeEnum;
use Tulia\Component\Datatable\Finder\AbstractDatatableFinder;
use Tulia\Component\Datatable\Finder\FinderContext;

/**
 * @author Adam Banaszkiewicz
 */
class DbalNodeDatatableFinder extends AbstractDatatableFinder implements NodeDatatableFinderInterface
{
    public function __construct(
        Connection $connection,
        private readonly TermFinderInterface $termFinder,
        private readonly TranslatorInterface $translator,
        private readonly NodeOptionsInterface $options,
        private readonly DbalNodeAttributesFinder $attributesFinder,
    ) {
        parent::__construct($connection);
    }

    public function getColumns(FinderContext $context): array
    {
        $viewContext = [
            'contentType' => $context['node_type'],
        ];

        $columns = [
            'id' => [
                'selector' => 'BIN_TO_UUID(tm.id)',
                'type' => 'uuid',
                'label' => 'ID',
            ],
            'title' => [
                'selector' => 'tl.title',
                'label' => 'title',
                'html_attr' => ['class' => 'col-title'],
                'sortable' => true,
                'view' => '@backend/node/parts/datatable/title.tpl',
                'view_context' => $viewContext,
            ],
            'published_at' => [
                'selector' => 'tm.published_at',
                'label' => 'date',
                'html_attr' => ['class' => 'col-date'],
                'sortable' => true,
                'view' => '@backend/node/parts/datatable/published.tpl',
            ],
        ];

        if ($this->supportsCategoryTaxonomy($context['node_type'])) {
            $columns['category'] = [
                'selector' => 'ttt.name',
                'html_attr' => ['class' => 'text-center'],
            ];
        }

        return $columns;
    }

    public function getFilters(FinderContext $context): array
    {
        $filters = [];

        $filters['title'] = [
            'label' => 'title',
            'type' => 'text',
            'selector' => 'tl.title',
        ];

        if ($this->supportsCategoryTaxonomy($context['node_type'])) {
            $filters['category'] = [
                'label' => 'category',
                'type' => 'single_select',
                'choices' => $this->createTaxonomyChoices($context),
                'selector' => 'BIN_TO_UUID(nit.term)'
            ];
        }

        /*$filters['status'] = [
            'label' => 'status',
            'type' => 'single_select',
            'choices' => $this->createStatusChoices(),
            'selector' => 'tm.status'
        ];*/

        return $filters;
    }

    public function prepareQueryBuilder(QueryBuilder $queryBuilder, FinderContext $context): QueryBuilder
    {
        $queryBuilder
            ->from('#__node', 'tm')
            ->addSelect('tm.type, tm.level, tm.parent_id, tl.slug, tm.status, tl.translated, tl.locale')
            ->innerJoin('tm', '#__node_translation', 'tl', 'tm.id = tl.node_id AND tl.locale = :locale')
            ->where('tm.type = :type')
            ->andWhere('tm.website_id = :website_id')
            ->setParameter('type', $context['node_type']->getCode(), PDO::PARAM_STR)
            ->setParameter('locale', $context['website']->getLocale()->getCode(), PDO::PARAM_STR)
            ->setParameter('website_id', Uuid::fromString($context['website']->getId())->toBinary(), PDO::PARAM_STR)
            ->addOrderBy('tm.level', 'ASC')
            ->addOrderBy('tm.created_at', 'DESC')
            ->addGroupBy('tm.id')
        ;

        if ($this->supportsCategoryTaxonomy($context['node_type'])) {
            $queryBuilder
                ->addSelect('ttt.name')
                ->leftJoin('tm', '#__node_in_term', 'nit', 'nit.node_id = tm.id AND nit.type = "main"')
                ->leftJoin('nit', '#__taxonomy_term_translation', 'ttt', 'ttt.term_id = nit.term')
            ;
        }

        return $queryBuilder;
    }

    public function prepareResult(array $result): array
    {
        $nodesId = array_column($result, 'id');
        $purposes = $this->fetchPurposes($nodesId);

        foreach ($result as $key => $val) {
            $result[$key]['purposes'] = $purposes[$val['id']] ?? [];
            $result[$key]['attributes'] = new LazyAttributesFinder($val['id'], $val['locale'], $this->attributesFinder);
        }

        return $result;
    }

    public function buildActions(FinderContext $context, array $row): array
    {
        $viewContext = [
            'contentType' => $context['node_type'],
        ];

        return [
            'main' => [
                'view' => '@backend/node/parts/datatable/links/edit-link.tpl',
                'view_context' => $viewContext,
            ],
            'preview' => [
                'view' => '@backend/node/parts/datatable/links/preview-link.tpl',
                'view_context' => $viewContext,
            ],
            'clone' => [
                'view' => '@backend/node/parts/datatable/links/clone-link.tpl',
                'view_context' => $viewContext,
            ],
            'delete' => [
                'view' => '@backend/node/parts/datatable/links/delete-link.tpl',
                'view_context' => $viewContext,
            ],
        ];
    }

    private function fetchPurposes(array $nodeIdList): array
    {
        $source = $this->connection->fetchAllAssociative('
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

    private function createTaxonomyChoices(FinderContext $context): array
    {
        $terms = $this->termFinder->find([
            'sort_hierarchical' => true,
            'locale' => $context['website']->getLocale()->getCode(),
            'website_id' => Uuid::fromString($context['website']->getId())->toBinary(),
            'taxonomy_type' => $this->options->get('category_taxonomy', $context['node_type']),
        ], TermFinderScopeEnum::INTERNAL);

        $result = [];

        foreach ($terms as $term) {
            $result[$term->getId()] = str_repeat('- ', $term->getLevel() - 1) . $term->getName();
        }

        return $result;
    }

    private function supportsCategoryTaxonomy(ContentType $contentType): bool
    {
        return (bool) $this->options->get('category_taxonomy', $contentType);
    }

    private function createStatusChoices(): array
    {
        // @todo
        return [];
        $statuses = [];

        foreach ($this->contentType->getStatuses() as $status) {
            $statuses[$status] = $this->translator->trans($status, [], 'node');
        }

        return $statuses;
    }
}
