<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Changeset;

use Tulia\Component\Theme\Customizer\Builder\Structure\StructureRegistry;
use Tulia\Component\Theme\Customizer\Changeset\Factory\ChangesetFactoryInterface;
use Tulia\Component\Theme\ThemeInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DefaultChangesetFactory
{
    public function __construct(
        private readonly ChangesetFactoryInterface $changesetFactory,
        private readonly StructureRegistry $structureRegistry,
    ) {
    }

    public function buildForTheme(ThemeInterface $theme): ChangesetInterface
    {
        $changeset = $this->changesetFactory->factory();
        $changeset->setTheme($theme->getName());

        $themes = [$theme->getName()];

        if ($theme->hasParent()) {
            array_unshift($themes, $theme->getParentName());
        }

        foreach ($this->structureRegistry->get($themes) as $section) {
            foreach ($section->getControls() as $control) {
                $changeset->set($control->getCode(), $control->getDefaultValue());
            }
        }

        return $changeset;
    }
}
