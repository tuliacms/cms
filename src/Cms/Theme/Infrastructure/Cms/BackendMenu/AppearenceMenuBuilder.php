<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Infrastructure\Cms\BackendMenu;

use Tulia\Cms\BackendMenu\Builder\BuilderInterface;
use Tulia\Cms\BackendMenu\Builder\Helper\BuilderHelperInterface;
use Tulia\Cms\BackendMenu\Builder\Registry\ItemRegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class AppearenceMenuBuilder implements BuilderInterface
{
    protected BuilderHelperInterface $helper;

    public function __construct(BuilderHelperInterface $helper)
    {
        $this->helper = $helper;
    }

    public function build(ItemRegistryInterface $registry, string $websiteId, string $locale): void
    {
        $registry->add('appearance_customize', [
            'label'  => $this->helper->trans('customize'),
            'link'   => $this->helper->generateUrl('backend.theme.customize'),
            'parent' => 'appearance',
        ]);
        $registry->add('appearance_widgets', [
            'label'  => $this->helper->trans('widgets'),
            'link'   => $this->helper->generateUrl('backend.widget'),
            'parent' => 'appearance',
        ]);
        $registry->add('appearance_themes', [
            'label'  => $this->helper->trans('themes'),
            'link'   => $this->helper->generateUrl('backend.theme'),
            'parent' => 'appearance',
        ]);
    }
}
