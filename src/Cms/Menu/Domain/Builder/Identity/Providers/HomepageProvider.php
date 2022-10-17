<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\Builder\Identity\Providers;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Tulia\Cms\Menu\Domain\Builder\Identity\Identity;
use Tulia\Cms\Menu\Domain\Builder\Identity\IdentityInterface;
use Tulia\Cms\Menu\Domain\Builder\Identity\IdentityProviderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class HomepageProvider implements IdentityProviderInterface
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly string $homepage = 'frontend.homepage',
    ) {
    }

    public function supports(string $type): bool
    {
        return $type === 'simple:homepage';
    }

    public function provide(string $type, string $identity, string $locale): ?IdentityInterface
    {
        return new Identity($this->urlGenerator->generate($this->homepage, ['_locale' => $locale]));
    }
}
