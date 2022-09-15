<?php

declare(strict_types=1);

namespace Tulia\Component\Hooks;

/**
 * @author Adam Banaszkiewicz
 */
interface HooksInterface
{
    public function doAction(string $name, mixed ...$parameters): ?string;

    public function addAction(string $name, string $callable, int $priority = 0): void;
}
