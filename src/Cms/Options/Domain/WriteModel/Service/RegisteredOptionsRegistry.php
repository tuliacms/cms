<?php

declare(strict_types=1);

namespace Tulia\Cms\Options\Domain\WriteModel\Service;

/**
 * @author Adam Banaszkiewicz
 */
final class RegisteredOptionsRegistry
{
    /**
     * @param iterable|OptionsProviderInterface[] $providers
     */
    public function __construct(
        private readonly iterable $providers,
    ) {
    }

    public function all(): array
    {
        $options = [];

        foreach ($this->providers as $provider) {
            $options += $provider->provide();
        }

        return $options;
    }
}
