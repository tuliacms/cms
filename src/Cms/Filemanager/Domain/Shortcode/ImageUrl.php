<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Domain\Shortcode;

use Tulia\Component\Shortcode\Compiler\ShortcodeCompilerInterface;
use Tulia\Component\Shortcode\ShortcodeInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ImageUrl implements ShortcodeCompilerInterface
{
    public function compile(ShortcodeInterface $shortcode): string
    {
        return "{{ image_url('{$shortcode->getParameter('id')}', '{$shortcode->getParameter('size')}') }}";
    }

    public function getAlias(): string
    {
        return 'image_url';
    }
}
