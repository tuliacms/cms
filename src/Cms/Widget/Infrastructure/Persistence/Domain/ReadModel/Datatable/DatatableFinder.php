<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Infrastructure\Persistence\Domain\ReadModel\Datatable;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PDO;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Widget\Domain\Catalog\Registry\WidgetRegistryInterface;
use Tulia\Component\Datatable\Finder\AbstractDatatableFinder;
use Tulia\Component\Datatable\Finder\FinderContext;
use Tulia\Component\Theme\ManagerInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DatatableFinder extends AbstractDatatableFinder
{
    public function __construct(
        Connection $connection,
        private readonly TranslatorInterface $translator,
        private readonly WidgetRegistryInterface $widgetRegistry,
        private readonly ManagerInterface $themeManager,
    ) {
        parent::__construct($connection);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationKey(): string
    {
        return __CLASS__;
    }

    /**
     * {@inheritdoc}
     */
    public function getColumns(): array
    {
        $context = [
            'widget_names' => $this->collectWidgetsNames(),
        ];

        return [
            'id' => [
                'selector' => 'BIN_TO_UUID(tm.id)',
                'type' => 'uuid',
                'label' => 'ID',
            ],
            'name' => [
                'selector' => 'tm.name',
                'label' => 'name',
                'sortable' => true,
                'html_attr' => ['class' => 'col-title'],
                'view' => '@backend/widget/parts/datatable/name.tpl',
                'view_context' => $context,
            ],
            'space' => [
                'selector' => 'tm.space',
                'label' => 'space',
                'translation_domain' => 'widgets',
                'sortable' => true,
                'value_translation' => $this->collectSpacesList(),
            ],
            'visibility' => [
                'selector' => 'tl.visibility',
                'label' => 'visibility',
                'sortable' => true,
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

    /**
     * {@inheritdoc}
     */
    public function getFilters(FinderContext $context): array
    {
        return [
            'name' => [
                'label' => 'name',
                'type' => 'text',
            ],
            'space' => [
                'label' => 'space',
                'translation_domain' => 'widgets',
                'type' => 'single_select',
                'selector' => 'tm.space',
                'choices' => $this->collectSpacesList(),
            ],
            'visibility' => [
                'label' => 'visibility',
                'type' => 'yes_no',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function prepareQueryBuilder(QueryBuilder $queryBuilder, FinderContext $context): QueryBuilder
    {
        $queryBuilder
            ->from('#__widget', 'tm')
            ->addSelect('tm.type')
            ->leftJoin('tm', '#__widget_translation', 'tl', 'tm.id = tl.widget_id AND tl.locale = :locale')
            ->setParameter('locale', $context->locale, PDO::PARAM_STR)
        ;

        if (false === $context->isDefaultLocale()) {
            $queryBuilder->addSelect('IF(ISNULL(tl.title), 0, 1) AS translated');
        }

        return $queryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function buildActions(array $row): array
    {
        return [
            'main' => '@backend/widget/parts/datatable/links/edit-link.tpl',
            'delete' => '@backend/widget/parts/datatable/links/delete-link.tpl',
        ];
    }

    private function collectSpacesList(): array
    {
        $config = $this->themeManager->getTheme()->getConfig();
        $spaces = $config->getWidgetSpaces();
        $spacesList = [];

        foreach ($spaces as $space) {
            $spacesList[$space['name']] = $this->translator->trans($space['label'], [], $config->getTranslationDomain());
        }

        return $spacesList;
    }

    private function collectWidgetsNames(): array
    {
        $widgetsNames = [];

        foreach ($this->widgetRegistry->all() as $widget) {
            $widgetsNames[$widget->getId()] = $this->translator->trans($widget->getName(), [], $widget->getTranslationDomain());
        }

        return $widgetsNames;
    }
}
