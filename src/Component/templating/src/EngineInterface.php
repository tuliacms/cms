<?php

declare(strict_types=1);

namespace Tulia\Component\Templating;

/**
 * @author Adam Banaszkiewicz
 */
interface EngineInterface
{
    public function render(ViewInterface $view): ?string;

    public function renderString(string $view, array $data = [], string $debugName = null): ?string;
}
