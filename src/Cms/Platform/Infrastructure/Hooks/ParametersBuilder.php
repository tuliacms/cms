<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Hooks;

use Psr\Container\ContainerInterface;
use Tulia\Component\Hooks\ParametersBuilderInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class ParametersBuilder implements ParametersBuilderInterface
{
    public function __construct(
        private readonly array $config,
        private readonly ContainerInterface $locator,
    ) {
    }

    public function build(string $action, array $parameters): array
    {
        if (false === isset($this->config[$action]['parameters']) || empty($this->config[$action]['parameters'])) {
            return $parameters;
        }

        foreach ($this->config[$action]['parameters'] as $config) {
            $param = null;

            if ($config['service']) {
                $param = $this->locator->get($config['service']);
            }

            if ($param) {
                if ($config['mode'] === 'append') {
                    $parameters[] = $param;
                } else {
                    array_unshift($parameters, $param);
                }
            }
        }

        return $parameters;
    }
}
