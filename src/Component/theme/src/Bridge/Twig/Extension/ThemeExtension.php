<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Bridge\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class ThemeExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'theme',
                [ThemeRuntime::class, 'theme'],
                ['is_safe' => [ 'html' ]]
            ),
            new TwigFunction(
                'customizer_get',
                [ThemeRuntime::class, 'customizerGet'],
                ['is_safe' => [ 'html' ]]
            ),
            new TwigFunction(
                'customizer_live_control',
                [ThemeRuntime::class, 'customizerLiveControl'],
                ['is_safe' => [ 'html' ]]
            ),
            new TwigFunction(
                'template',
                [ThemeRuntime::class, 'themeTemplate'],
                ['is_safe' => [ 'html' ]]
            ),
            new TwigFunction(
                'parent_template',
                [ThemeRuntime::class, 'parentThemeTemplate'],
                ['is_safe' => [ 'html' ]]
            ),
        ];
    }
}
