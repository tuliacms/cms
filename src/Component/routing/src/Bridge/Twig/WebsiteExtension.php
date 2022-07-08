<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\Bridge\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class WebsiteExtension extends AbstractExtension
{
    public function __construct(
        protected RequestStack $requestStack
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('locales', function () {
                return $this->requestStack->getCurrentRequest()->attributes->get('website')->getLocales();
            }, [
                 'is_safe' => [ 'html' ]
            ]),
            new TwigFunction('locale', function () {
                return $this->requestStack->getCurrentRequest()->attributes->get('website')->getLocale();
            }, [
                 'is_safe' => [ 'html' ]
            ]),
            new TwigFunction('current_website', function () {
                return $this->requestStack->getCurrentRequest()->attributes->get('website');
            }, [
                 'is_safe' => [ 'html' ]
            ]),
        ];
    }
}
