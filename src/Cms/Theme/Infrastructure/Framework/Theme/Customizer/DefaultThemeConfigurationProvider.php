<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Infrastructure\Framework\Theme\Customizer;

use Tulia\Cms\Theme\Domain\WriteModel\Service\DefaultThemeConfigurationProviderInterface;
use Tulia\Component\Theme\Customizer\Builder\Structure\StructureRegistry;

/**
 * @author Adam Banaszkiewicz
 */
final class DefaultThemeConfigurationProvider implements DefaultThemeConfigurationProviderInterface
{
    public function __construct(
        private readonly StructureRegistry $registry,
    ) {
    }

    public function provideDefaultValues(string $theme): array
    {
        $values = [];

        foreach ($this->registry->get($theme) as $section) {
            foreach ($section->getControls() as $control) {
                $values[$control->getCode()] = $control->getDefaultValue();
            }
        }

        return $values;
    }

    public function provideMultilingualControls(string $theme): array
    {
        $controls = [];

        foreach ($this->registry->get($theme) as $section) {
            foreach ($section->getControls() as $control) {
                if ($control->isMultilingual()) {
                    $controls[] = $control->getCode();
                }
            }
        }

        return $controls;
    }
}
