<?php

declare(strict_types=1);

namespace Tulia\Component\Theme;

/**
 * @author Adam Banaszkiewicz
 */
final class ThemeTemplateProvider implements ThemeTemplateProviderInterface
{
    public function __construct(
        private readonly ManagerInterface $manager,
    ) {
    }

    public function template(string $name): string
    {
        $theme = $this->manager->getTheme();

        return $theme->getViewsDirectory().'/'.$name;
    }

    public function parentTemplate(string $name): string
    {
        $theme = $this->manager->getTheme();

        if ($theme->hasParent()) {
            $theme = $theme->getParent();
        }

        return $theme->getViewsDirectory().'/'.$name;
    }
}
