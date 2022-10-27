<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Persistence\Doctrine\Dbal\ReadModel;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PDO;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Content\Type\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\Node\Domain\ReadModel\Datatable\NodeDatatableFinderInterface;
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
                'view' => '@backend/node/parts/datatable/title.tpl',
                'view_context' => $viewContext,
            ],
            'published_at' => [
                'selector' => 'tm.published_at',
                'label' => 'date',
                'html_attr' => ['class' => 'col-date'],
                'view' => '@backend/node/parts/datatable/published.tpl',
            ],
        ];

        if ($this->supportsCategoryTaxonomy()) {
            $columns['category'] = [
                'selector' => 'COALESCE(nt.name, ntl.name)',
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
            'selector' => 'COALESCE(tl.title, tm.title)'
        ];

        if ($this->supportsCategoryTaxonomy()) {
            $filters['category'] = [
                'label' => 'category',
                'type' => 'single_select',
                'choices' => $this->createTaxonomyChoices(),
                'selector' => 'nt.id'
            ];
        }

        $filters['status'] = [
            'label' => 'status',
            'type' => 'single_select',
            'choices' => $this->createStatusChoices(),
            'selector' => 'tm.status'
        ];

        return $filters;
    }

    public function prepareQueryBuilder(QueryBuilder $queryBuilder, FinderContext $context): QueryBuilder
    {
        $queryBuilder
            ->from('#__node', 'tm')
            ->addSelect('tm.type, tm.level, tm.parent_id, tl.slug, tm.status, tl.translated, GROUP_CONCAT(tnhf.purpose SEPARATOR \',\') AS purposes')
            ->leftJoin('tm', '#__node_translation', 'tl', 'tm.id = tl.node_id AND tl.locale = :locale')
            ->leftJoin('tm', '#__node_has_purpose', 'tnhf', 'tm.id = tnhf.node_id')
            ->where('tm.type = :type')
            ->andWhere('tm.website_id = :website_id')
            ->setParameter('type', $context['node_type']->getCode(), PDO::PARAM_STR)
            ->setParameter('locale', $context['website']->getLocale()->getCode(), PDO::PARAM_STR)
            ->setParameter('website_id', Uuid::fromString($context['website']->getId())->toBinary(), PDO::PARAM_STR)
            ->addOrderBy('tm.level', 'ASC')
            ->addGroupBy('tm.id')
        ;

        if ($this->supportsCategoryTaxonomy()) {
            $queryBuilder
                ->addSelect('nt.id AS term_id, nt.type AS taxonomy_type')
                ->leftJoin('tm', '#__node_term_relationship', 'ntr', 'ntr.node_id = tm.id')
                ->leftJoin('ntr', '#__term', 'nt', 'nt.id = ntr.term_id')
                ->leftJoin('nt', '#__term_lang', 'ntl', 'ntl.term_id = nt.id AND ntl.locale = :locale');
        }

        return $queryBuilder;
    }

    public function prepareResult(array $result): array
    {
        foreach ($result as $key => $row) {
            $result[$key]['purposes'] = array_filter(explode(',', (string) $row['purposes']));
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
            'delete' => [
                'view' => '@backend/node/parts/datatable/links/delete-link.tpl',
                'view_context' => $viewContext,
            ],
        ];
    }

    private function createTaxonomyChoices(): array
    {
        $terms = $this->termFinder->find([
            'sort_hierarchical' => true,
        ], TermFinderScopeEnum::INTERNAL);

        $result = [];

        foreach ($terms as $term) {
            $result[$term->getId()] = str_repeat('- ', $term->getLevel() - 1) . $term->getName();
        }

        return $result;
    }

    private function supportsCategoryTaxonomy(): bool
    {
        // @todo
        return false;
        foreach ($this->contentType->getTaxonomies() as $taxonomy) {
            if ($taxonomy['taxonomy'] === 'category') {
                return true;
            }
        }

        return false;
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