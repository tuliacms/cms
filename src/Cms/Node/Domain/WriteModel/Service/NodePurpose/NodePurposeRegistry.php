<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Service\NodePurpose;

use Tulia\Cms\Node\Domain\WriteModel\Exception\NodePurposeNotFoundException;

/**
 * @author Adam Banaszkiewicz
 */
class NodePurposeRegistry implements NodePurposeRegistryInterface
{
    /**
     * @var NodePurposeProviderInterface[]
     */
    private iterable $providers;

    private array $flags = [];

    public function __construct(iterable $providers)
    {
        $this->providers = $providers;
    }

    public function all(): array
    {
        $this->resolveFlags();

        return $this->flags;
    }

    public function isSingular(string $name): bool
    {
        $this->resolveFlags();

        foreach ($this->flags as $key => $flag) {
            if ($key === $name) {
                return (bool) $flag['singular'];
            }
        }

        throw NodePurposeNotFoundException::fromName($name);
    }

    public function has(string $name): bool
    {
        $this->resolveFlags();

        return isset($this->flags[$name]);
    }

    public function get(string $name): array
    {
        $this->resolveFlags();

        return $this->flags[$name] ?? [];
    }

    private function resolveFlags(): void
    {
        if ($this->flags !== []) {
            return;
        }

        foreach ($this->providers as $provider) {
            $this->flags[] = $provider->provide();
        }

        $this->flags = array_merge(...$this->flags);

        foreach ($this->flags as $type => $flag) {
            $this->flags[$type] = array_merge([
                'singular' => false,
                'label' => 'flagType.' . $type,
            ], $this->flags[$type]);
        }
    }
}
