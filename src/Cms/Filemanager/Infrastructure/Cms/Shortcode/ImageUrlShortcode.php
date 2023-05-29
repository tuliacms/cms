<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Infrastructure\Cms\Shortcode;

use Symfony\Component\Uid\Uuid;
use Tulia\Component\Shortcode\Compiler\ShortcodeCompilerInterface;
use Tulia\Component\Shortcode\ShortcodeInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ImageUrlShortcode implements ShortcodeCompilerInterface
{
    public function compile(ShortcodeInterface $shortcode): string
    {
        try {
            Uuid::fromString($shortcode->getParameter('id'));
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException(sprintf('Invalid UUID of "image_url" shortcode given: "%s"', $shortcode->getParameter('id')), $e->getCode(), $e);
        }

        return "{{ image_url('{$shortcode->getParameter('id')}', { size: '{$shortcode->getParameter('size')}' }) }}";
    }

    public function getAlias(): string
    {
        return 'image_url';
    }
}
