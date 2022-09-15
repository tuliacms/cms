<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\Builder\Identity\Providers;

use Tulia\Cms\Menu\Domain\Builder\Identity\Identity;
use Tulia\Cms\Menu\Domain\Builder\Identity\IdentityInterface;
use Tulia\Cms\Menu\Domain\Builder\Identity\IdentityProviderInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Adam Banaszkiewicz
 */
class HomepageProvider implements IdentityProviderInterface
{
    public function __construct(
        private readonly RouterInterface $router,
        private readonly string $homepage = 'homepage',
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function supports(string $type): bool
    {
        return $type === 'simple:homepage';
    }

    /**
     * {@inheritdoc}
     */
    public function provide(string $type, string $identity, string $locale): ?IdentityInterface
    {
        return new Identity($this->router->generate($this->homepage, ['_locale' => $locale]));
    }
}
