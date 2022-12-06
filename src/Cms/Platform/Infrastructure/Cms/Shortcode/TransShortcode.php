<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Cms\Shortcode;

use Tulia\Component\Shortcode\Compiler\ShortcodeCompilerInterface;
use Tulia\Component\Shortcode\ShortcodeInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class TransShortcode implements ShortcodeCompilerInterface
{
    public function compile(ShortcodeInterface $shortcode): string
    {
        return <<<EOF
{{ '{$shortcode->getParameter('key')}'|trans({}, '{$shortcode->getParameter('domain', 'messages')}') }}
EOF;
    }

    public function getAlias(): string
    {
        return 'trans';
    }
}
