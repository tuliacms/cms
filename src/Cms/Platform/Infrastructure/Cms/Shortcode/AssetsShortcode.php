<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Cms\Shortcode;

use Tulia\Component\Shortcode\Compiler\ShortcodeCompilerInterface;
use Tulia\Component\Shortcode\ShortcodeInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class AssetsShortcode implements ShortcodeCompilerInterface
{
    public function compile(ShortcodeInterface $shortcode): string
    {
        if (!$shortcode->getParameter('names')) {
            return '';
        }

        $names = str_replace(",", "','", $shortcode->getParameter('names'));

        return "{% assets ['{$names}'] %}";
    }

    public function getAlias(): string
    {
        return 'assets';
    }
}
