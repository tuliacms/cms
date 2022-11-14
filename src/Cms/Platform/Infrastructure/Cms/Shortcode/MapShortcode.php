<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Cms\Shortcode;

use Tulia\Component\Shortcode\Compiler\ShortcodeCompilerInterface;
use Tulia\Component\Shortcode\ShortcodeInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class MapShortcode implements ShortcodeCompilerInterface
{
    public function compile(ShortcodeInterface $shortcode): string
    {
        return <<<EOF
{% assets ['tulia.simplemap'] %}
<div
    class="tulia-simplemap"
    id="tulia-simplemap-{{uniqid()}}"
    data-zoom="{$shortcode->getParameter('zoom')}"
    data-lat="{$shortcode->getParameter('lat')}"
    data-lng="{$shortcode->getParameter('lng')}"
    style="height:{$shortcode->getParameter('height')}px"
></div>
EOF;
    }

    public function getAlias(): string
    {
        return 'map';
    }
}
