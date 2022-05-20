<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Attributes\Domain\ReadModel\ValueRender;

/**
 * @author Adam Banaszkiewicz
 */
interface ValueRendererInterface
{
    public function render(?string $value, array $context): RenderableValueInterface;
}
