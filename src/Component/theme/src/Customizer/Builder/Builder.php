<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Builder;

use Tulia\Component\Theme\Customizer\Builder\Rendering\CustomizerView;
use Tulia\Component\Theme\Customizer\Builder\Rendering\SectionRendererInterface;
use Tulia\Component\Theme\Customizer\Builder\Structure\StructureRegistry;
use Tulia\Component\Theme\Customizer\Changeset\ChangesetInterface;
use Tulia\Component\Theme\ThemeInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Builder implements BuilderInterface
{
    public function __construct(
        private readonly StructureRegistry $structureRegistry,
        private readonly SectionRendererInterface $sectionRenderer,
    ) {
    }

    public function build(ChangesetInterface $changeset, ThemeInterface $theme): CustomizerView
    {
        $themes = [$theme->getName()];

        if ($theme->hasParent()) {
            array_unshift($themes, $theme->getParentName());
        }

        $structure = $this->structureRegistry->get($themes);

        $result = [];

        foreach ($structure as $section) {
            $result[] = $this->sectionRenderer->render($structure, $section, $changeset);
        }

        return new CustomizerView(implode('', $result), $structure);
    }
}
