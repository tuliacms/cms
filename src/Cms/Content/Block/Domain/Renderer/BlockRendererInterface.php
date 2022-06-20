<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Block\Domain\Renderer;

/**
 * @author Adam Banaszkiewicz
 */
interface BlockRendererInterface
{
    public function render(array $model): string;

    public function renderBlock(array $block): string;
}
