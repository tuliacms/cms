<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Attributes\Domain\ReadModel\Service\ValueRendering;

/**
 * @author Adam Banaszkiewicz
 */
interface ValueRendererInterface
{
    public function render(?string $value, array $context): RenderableValueInterface;
}
