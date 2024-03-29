<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Infrastructure\Cms\Shortcode;

use Symfony\Component\Uid\Uuid;
use Tulia\Cms\Filemanager\Domain\Generator\Html;
use Tulia\Component\Shortcode\Compiler\ShortcodeCompilerInterface;
use Tulia\Component\Shortcode\ShortcodeInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ImageShortcode implements ShortcodeCompilerInterface
{
    public function compile(ShortcodeInterface $shortcode): string
    {
        if ($src = $shortcode->getParameter('src')) {
            return $this->compileSrc($shortcode, $src);
        }

        if ($id = $shortcode->getParameter('id')) {
            try {
                Uuid::fromString($shortcode->getParameter('id'));
            } catch (\InvalidArgumentException $e) {
                throw new \InvalidArgumentException(sprintf('Invalid UUID of "image" shortcode given: "%s"', $shortcode->getParameter('id')), $e->getCode(), $e);
            }

            return $this->compileId($shortcode, $id);
        }

        return '';
    }

    public function getAlias(): string
    {
        return 'image';
    }

    private function compileSrc(ShortcodeInterface $shortcode, string $src): string
    {
        $attributes = $this->collectAttributes($shortcode);
        $attributes['src'] = "{{ asset('{$src}') }}";

        return (new Html())->generateImageTag($attributes);
    }

    private function compileId(ShortcodeInterface $shortcode, string $id): string
    {
        $size    = $shortcode->getParameter('size');
        $version = $shortcode->getParameter('version');
        $attributes = $this->collectAttributes($shortcode);

        $hash = [];

        foreach ($attributes as $name => $val) {
            $hash[] = "$name: '$val'";
        }

        $hash = '{' . implode(', ', $hash) . '}';

        return "{{ image('{$id}', {attributes: {$hash}, size: '{$size}', version: '{$version}'}) }}";
    }

    private function collectAttributes(ShortcodeInterface $shortcode): array
    {
        $attributes = array_filter([
            'title'   => $shortcode->getParameter('title'),
            'id'      => $shortcode->getParameter('html-id'),
            'class'   => $shortcode->getParameter('html-class'),
            'caption' => $shortcode->getParameter('caption'),
        ]);
        $attributes['alt'] = $shortcode->getParameter('alt');

        return $attributes;
    }
}
