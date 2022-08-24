<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\Parameters;

/**
 * @author Adam Banaszkiewicz
 */
interface ParametersProviderInterface
{
    public function provide(): array;
}
