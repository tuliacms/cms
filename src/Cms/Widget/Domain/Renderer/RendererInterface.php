<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Domain\Renderer;

/**
 * @author Adam Banaszkiewicz
 */
interface RendererInterface
{
    public function forId(string $id, string $websiteId, string $locale): string;

    public function forSpace(string $space, string $websiteId, string $locale): string;
}
