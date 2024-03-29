<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\Builder\Identity;

/**
 * @author Adam Banaszkiewicz
 */
class Registry implements RegistryInterface
{
    /**
     * @var iterable<IdentityProviderInterface>
     */
    protected $providers = [];

    /**
     * @param iterable $providers
     */
    public function __construct(iterable $providers = [])
    {
        $this->providers = $providers;
    }

    /**
     * {@inheritdoc}
     */
    public function all(): iterable
    {
        return $this->providers;
    }

    /**
     * {@inheritdoc}
     */
    public function add(IdentityProviderInterface $provider): void
    {
        $this->providers[] = $provider;
    }

    /**
     * {@inheritdoc}
     */
    public function provide(string $type, string $identity, string $locale): ?IdentityInterface
    {
        foreach ($this->providers as $provider) {
            if ($provider->supports($type)) {
                $id = $provider->provide($type, $identity, $locale);

                if ($id instanceof IdentityInterface) {
                    $id->setId($identity);
                    $id->setType($type);
                }

                return $id;
            }
        }

        return null;
    }
}
