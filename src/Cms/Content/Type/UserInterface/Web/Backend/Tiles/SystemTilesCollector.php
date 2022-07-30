<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\UserInterface\Web\Backend\Tiles;

use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Homepage\UserInterface\Web\Backend\Tiles\DashboardTilesCollection;
use Tulia\Cms\Homepage\UserInterface\Web\Backend\Tiles\DashboardTilesCollector;

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
            ->add('content_model', [
                'name' => $this->translator->trans('contentTypes', [], 'content_builder'),
                'route' => 'backend.content.type.homepage',
                'icon' => 'fas fa-box',
            ])
        ;
    }

    public function supports(string $group): bool
    {
        return $group === 'system';
    }
}
