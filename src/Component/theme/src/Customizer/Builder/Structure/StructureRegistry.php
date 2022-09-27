<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Builder\Structure;

/**
 * @author Adam Banaszkiewicz
 */
class StructureRegistry
{
    private array $structureByThemes;

    public function addForTheme(string $theme, array $structure): void
    {
        $this->structureByThemes[$theme] = $structure;
    }

    /**
     * @return Section[]
     */
    public function get(array $themes): array
    {
        $result = [];

        foreach ($themes as $theme) {
            if (isset($this->structureByThemes[$theme]) === false) {
                continue;
            }

            foreach ($this->structureByThemes[$theme] as $section) {
                if (isset($result[$section['code']])) {
                    $result[$section['code']]->merge(Section::fromArray($section));
                } else {
                    $result[$section['code']] = Section::fromArray($section);
                }
            }
        }

        return $result;
    }
}
