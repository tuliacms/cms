<?php

declare(strict_types=1);

namespace Tulia\Cms\Extension\Infrastructure\Cms\Tiles;

use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Homepage\UserInterface\Web\Backend\Tiles\DashboardTilesCollection;
use Tulia\Cms\Homepage\UserInterface\Web\Backend\Tiles\DashboardTilesCollector;

/**
 * @author Adam Banaszkiewicz
 */
class SystemTilesCollector implements DashboardTilesCollector
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function collect(DashboardTilesCollection $collection): void
    {
        $collection
            ->add('modules', [
                'name' => $this->translator->trans('modules', [], 'extension'),
                'route' => 'backend.extension.homepage',
                'icon' => 'fas fa-box-open',
            ])
        ;
    }

    public function supports(string $group): bool
    {
        return $group === 'system';
    }
}
