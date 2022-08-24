<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\Parameters;

/**
 * @author Adam Banaszkiewicz
 */
final class ArrayParametersProvider implements ParametersProviderInterface
{
    public function __construct(
        private readonly array $parameters
    ) {
    }

    public function provide(): array
    {
        return $this->parameters;
    }
}
