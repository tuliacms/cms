<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Cms\Menu;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Tulia\Cms\Menu\Domain\Builder\Identity\Identity;
use Tulia\Cms\Menu\Domain\Builder\Identity\IdentityInterface;
use Tulia\Cms\Menu\Domain\Builder\Identity\IdentityProviderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class IdentityProvider implements IdentityProviderInterface
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function supports(string $type): bool
    {
        return strncmp($type, 'node:', 5) === 0;
    }

    public function provide(string $type, string $identity, string $locale): ?IdentityInterface
    {
        [, $id] = explode(':', $type);

        return new Identity($this->urlGenerator->generate(sprintf('frontend.node.%s.%s', $id, $identity), ['_locale' => $locale]), [ 'node-' . $identity ]);
    }
}
