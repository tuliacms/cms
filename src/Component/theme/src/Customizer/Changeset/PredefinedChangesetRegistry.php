<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Changeset;

use Tulia\Component\Theme\Enum\ChangesetTypeEnum;
use Tulia\Component\Theme\ThemeInterface;

/**
 * @author Adam Banaszkiewicz
 */
class PredefinedChangesetRegistry
{
    private array $structureByThemes;

    public function addForTheme(string $theme, array $structure)
    {
        $this->structureByThemes[$theme] = $structure;
    }

    /**
     * @return ChangesetInterface[]
     */
    public function get(ThemeInterface $theme): array
    {
        $changesets = [];

        if (isset($this->structureByThemes[$theme->getName()])) {
            $changesets = $this->structureByThemes[$theme->getName()];
        }

        if ($theme->hasParent() && isset($this->structureByThemes[$theme->getParent()->getName()])) {
            $changesets += $this->structureByThemes[$theme->getParent()->getName()];
        }

        $result = [];

        foreach ($changesets as $id => $changeset) {
            $namedChangeset = new NamedChangeset($id, ChangesetTypeEnum::PREDEFINED, $changeset);
            $namedChangeset->setLabel($changeset['label']);
            $namedChangeset->setDescription($changeset['description']);
            $namedChangeset->setTranslationDomain($changeset['translation_domain']);
            $result[] = $namedChangeset;
        }

        return $result;
    }
}
