<?php

declare(strict_types=1);

namespace Tulia\Cms\Settings\UserInterface\Web\Backend\BackendMenu;

use Tulia\Cms\BackendMenu\Builder\BuilderInterface;
use Tulia\Cms\BackendMenu\Builder\Helper\BuilderHelperInterface;
use Tulia\Cms\BackendMenu\Builder\Registry\ItemRegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class SettingsMenuBuilder implements BuilderInterface
{
    protected BuilderHelperInterface $helper;

    public function __construct(BuilderHelperInterface $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param ItemRegistryInterface $registry
     */
    public function build(ItemRegistryInterface $registry, string $websiteId, string $locale): void
    {
        $registry->add('settings', [
            'label'    => $this->helper->trans('settings'),
            'link'     => $this->helper->generateUrl('backend.settings'),
            'priority' => 500,
            'icon'     => 'fas fa-cogs',
            'parent'   => 'section_administration',
        ]);
    }
}
