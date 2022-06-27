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

    private array $purposes = [];

    public function __construct(iterable $providers)
    {
        $this->providers = $providers;
    }

    public function all(): array
    {
        $this->resolvePurposes();

        return $this->purposes;
    }

    public function isSingular(string $name): bool
    {
        $this->resolvePurposes();

        foreach ($this->purposes as $key => $purpose) {
            if ($key === $name) {
                return (bool) $purpose['singular'];
            }
        }

        throw NodePurposeNotFoundException::fromName($name);
    }

    public function has(string $name): bool
    {
        $this->resolvePurposes();

        return isset($this->purposes[$name]);
    }

    public function get(string $name): array
    {
        $this->resolvePurposes();

        return $this->purposes[$name] ?? [];
    }

    public function add(string $name, bool $singular): void
    {
        $this->purposes[$name] = [
            'singular' => $singular,
            'label' => 'purposeType.' . $name,
        ];
    }

    private function resolvePurposes(): void
    {
        if ($this->purposes !== []) {
            return;
        }

        foreach ($this->providers as $provider) {
            $this->purposes[] = $provider->provide();
        }

        $this->purposes = array_merge(...$this->purposes);

        foreach ($this->purposes as $type => $purpose) {
            $this->purposes[$type] = array_merge([
                'singular' => false,
                'label' => 'purposeType.' . $type,
            ], $this->purposes[$type]);
        }
    }
}
