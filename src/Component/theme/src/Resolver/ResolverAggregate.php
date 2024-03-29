<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Resolver;

use Tulia\Component\Theme\Configuration\ConfigurationInterface;
use Tulia\Component\Theme\ThemeInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ResolverAggregate implements ResolverAggregateInterface
{
    /** @var ResolverInterface[] */
    protected iterable $resolvers;

    public function __construct(iterable $resolvers)
    {
        $this->resolvers = $resolvers;
    }

    public function resolve(ConfigurationInterface $configuration, ThemeInterface $theme, string $websiteId, string $locale): void
    {
        foreach ($this->resolvers as $resolver) {
            $resolver->resolve($configuration, $theme, $websiteId, $locale);
        }
    }
}
