<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Domain\FieldType;

use Tulia\Component\Shortcode\ShortcodeInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface FieldParserInterface
{
    public function getAlias(): string;

    public function parseShortcode(ShortcodeInterface $shortcode): array;

    public function getDefinition(): array;
}
