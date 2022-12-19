<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\ReadModel\FieldChoicesProvider;

/**
 * @author Adam Banaszkiewicz
 */
final class FieldChoicesProviderRegistry
{
    /**
     * @var FieldChoicesProviderInterface[]
     */
    private array $providers = [];

    public function addProvider(string $id, FieldChoicesProviderInterface $provider): void
    {
        $this->providers[$id] = $provider;
    }

    public function has(string $type): bool
    {
        return isset($this->providers[$type]);
    }

    public function get(string $type): FieldChoicesProviderInterface
    {
        return $this->providers[$type];
    }

    public function all(): array
    {
        return $this->providers;
    }
}
