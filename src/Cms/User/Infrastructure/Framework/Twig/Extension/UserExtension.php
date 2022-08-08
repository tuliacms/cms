<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Framework\Twig\Extension;

use Tulia\Cms\User\Application\Service\AuthenticatedUserProviderInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class UserExtension extends AbstractExtension
{
    protected AuthenticatedUserProviderInterface $authenticatedUserProvider;

    public function __construct(AuthenticatedUserProviderInterface $authenticatedUserProvider)
    {
        $this->authenticatedUserProvider  = $authenticatedUserProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('user', function () {
                return $this->authenticatedUserProvider->getUser();
            }, [
                'is_safe' => [ 'html' ]
            ]),
            new TwigFunction('user_locale', function () {
                return $this->authenticatedUserProvider->getUser()->getLocale();
            }, [
                'is_safe' => [ 'html' ]
            ]),
            new TwigFunction('user_initials', function () {
                $user = $this->authenticatedUserProvider->getUser();

                if ($user->getName()) {
                    $parts = explode(' ', $user->getName());
                } else {
                    [$username, ] = explode('@', $user->getEmail());
                    $parts = explode('.', $username);
                }

                return strtoupper(substr(implode(array_map(
                    static fn($v) => $v[0],
                    $parts
                )), 0, 2));
            }, [
                'is_safe' => [ 'html' ]
            ]),
        ];
    }
}
