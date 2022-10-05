<?php

declare(strict_types=1);

namespace Tulia\Component\Theme;

use Tulia\Component\Theme\Configuration\ConfigurationInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface ThemeInterface
{
    public function getName(): string;

    public function getConfig(): ConfigurationInterface;

    public function getParent(): ThemeInterface;

    public function getParentName(): ?string;

    public function hasParent(): bool;

    public function setParentThemeLoader(callable $loader): void;

    public function getPreviewDirectory(): ?string;
}
