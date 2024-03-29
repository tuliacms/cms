<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\Menu;

use Tulia\Cms\Menu\Domain\Builder\Identity\Identity;
use Tulia\Cms\Menu\Domain\Builder\Identity\IdentityInterface;
use Tulia\Cms\Menu\Domain\Builder\Identity\IdentityProviderInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Adam Banaszkiewicz
 */
class IdentityProvider implements IdentityProviderInterface
{
    protected RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(string $type): bool
    {
        return strncmp($type, 'term:', 5) === 0;
    }

    /**
     * {@inheritdoc}
     */
    public function provide(string $type, string $identity, string $locale): ?IdentityInterface
    {
        [, $id] = explode(':', $type);

        return new Identity($this->router->generate(sprintf('term.%s.%s', $id, $identity), ['_locale' => $locale]), [ 'term-' . $identity ]);
    }
}
