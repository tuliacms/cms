<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\Builder\Identity;

/**
 * @author Adam Banaszkiewicz
 */
interface RegistryInterface
{
    /**
     * @return IdentityProviderInterface[]
     */
    public function all(): iterable;

    public function add(IdentityProviderInterface $provider): void;

    public function provide(string $type, string $identity, string $locale): ?IdentityInterface;
}
