<?php

declare(strict_types=1);

namespace Tulia\Cms\ImportExport\UserInterface\Web\Tiles;

use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Homepage\UserInterface\Web\Backend\Tiles\DashboardTilesCollection;
use Tulia\Cms\Homepage\UserInterface\Web\Backend\Tiles\DashboardTilesCollector;

/**
 * @author Adam Banaszkiewicz
 */
class ToolsTilesCollector implements DashboardTilesCollector
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function collect(DashboardTilesCollection $collection): void
    {
        $collection
            ->add('importer', [
                'name' => $this->translator->trans('importer', [], 'import_export'),
                'route' => 'backend.import_export.importer',
                'icon' => 'fas fa-file-import',
            ])
            ->add('exporter', [
                'name' => $this->translator->trans('exporter', [], 'import_export'),
                'route' => 'backend.import_export.exporter',
                'icon' => 'fas fa-file-export',
            ])
        ;
    }

    public function supports(string $group): bool
    {
        return $group === 'tools';
    }
}
