<?php

declare(strict_types=1);

namespace Tulia\Component\Hooks;

/**
 * @author Adam Banaszkiewicz
 */
interface ParametersBuilderInterface
{
    public function build(string $action, array $parameters): array;
}
