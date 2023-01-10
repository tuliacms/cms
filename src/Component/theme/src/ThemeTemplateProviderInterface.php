<?php

declare(strict_types=1);

namespace Tulia\Component\Theme;

/**
 * @author Adam Banaszkiewicz
 */
interface ThemeTemplateProviderInterface
{
    public function template(string $name): string;

    public function parentTemplate(string $name): string;
}
