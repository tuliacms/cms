<?php

declare(strict_types=1);

namespace Tulia\Cms\Homepage\UserInterface\Web\Backend\Tiles;

use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class SystemTilesCollector implements DashboardTilesCollector
{
    public function __construct(
        protected TranslatorInterface $translator
    ) {
    }

    public function collect(DashboardTilesCollection $collection): void
    {
        $collection
            ->add('users', [
                'name' => $this->translator->trans('users'),
                'route' => 'backend.user',
                'icon' => 'fas fa-users',
            ])->add('websites', [
                'name' => $this->translator->trans('websites'),
                'route' => 'backend.website',
                'icon' => 'fas fa-globe',
            ])
        ;
    }

    public function supports(string $group): bool
    {
        return $group === 'system';
    }
}
