<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Infrastructure\Cms\Shortcode;

use Tulia\Component\Shortcode\Compiler\ShortcodeCompilerInterface;
use Tulia\Component\Shortcode\ShortcodeInterface;

/**
 * @author Adam Banaszkiewicz
 */
class GalleryShortcode implements ShortcodeCompilerInterface
{
    public function compile(ShortcodeInterface $shortcode): string
    {
        $ids = explode(',', $shortcode->getParameter('ids'));
        $ids = array_map(function ($id) {
            $id = trim($id);
            return "'{$id}'";
        }, $ids);
        $ids = implode(',', $ids);

        return "{{ gallery([{$ids}]) }}";
    }

    public function getAlias(): string
    {
        return 'gallery';
    }
}
