<?php

declare(strict_types=1);

namespace Tulia\Component\Hooks;

use Psr\Container\ContainerInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class Hooks implements HooksInterface
{
    private array $hooks = [];

    public function __construct(
        private readonly ContainerInterface $locator,
    ) {
    }

    public function addAction(string $name, string $callable, int $priority = 0): void
    {
        $this->hooks['action'][$name][$priority][] = $callable;
    }

    public function doAction(string $name, ...$parameters): ?string
    {
        if (!isset($this->hooks['action'][$name])) {
            return null;
        }

        ksort($this->hooks['action'][$name]);

        $result = [];

        foreach ($this->hooks['action'][$name] as $priority => $callbacks) {
            $result[] = array_map(fn($callable) => $this->callService($callable, ...$parameters), $callbacks);
        }

        return implode('', array_merge(...$result));
    }

    private function callService(string $callable, mixed ...$parameters): ?string
    {
        [$service, $method] = explode('::', $callable);

        return $this->locator->get($service)->{$method}(...$parameters);
    }
}
