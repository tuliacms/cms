<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Block\Domain\Shortcode;

use Tulia\Component\Shortcode\Compiler\ShortcodeCompilerInterface;
use Tulia\Component\Shortcode\ShortcodeInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DynamicBlock implements ShortcodeCompilerInterface
{
    /**
     * {@inheritdoc}
     */
    public function compile(ShortcodeInterface $shortcode): string
    {
        $parameters = [
            'type' => $shortcode->getParameter('type'),
            'fields' => $shortcode->getParameters(),
            'visible' => true,
        ];

        unset($parameters['fields']['type']);

        $parameters = base64_encode(json_encode($parameters));
        return "{{ dynamic_block_render('{$parameters}') }}";
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias(): string
    {
        return 'dynamic_block';
    }
}
