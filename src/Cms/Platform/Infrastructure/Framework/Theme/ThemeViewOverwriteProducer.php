<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Theme;

use Tulia\Component\Theme\ManagerInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class ThemeViewOverwriteProducer
{
    public function __construct(
        private readonly ManagerInterface $themeManager,
    ) {
    }

    public function produce(string $type, array $currentViews): array
    {
        $newViews = [];
        $theme = $this->themeManager->getTheme();

        foreach ($currentViews as $val) {
            if ($type === 'widget' && strncmp($val, '@widget/', 8) === 0) {
                $newViews[] = str_replace('@widget/', $theme->getViewsDirectory().'/overwrite/widget/', $val);
            }
            if ($type === 'cms' && strncmp($val, '@cms/', 5) === 0) {
                $newViews[] = str_replace('@cms/', $theme->getViewsDirectory().'/overwrite/cms/', $val);
            }

            $newViews[] = $val;
        }

        return $newViews;
    }
}
