<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\Bridge\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Component\Routing\Website\WebsiteRegistryInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class WebsiteExtension extends AbstractExtension
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly WebsiteRegistryInterface $websiteRegistry,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('trans_locale', function (string $code) {
                return $this->translator->trans('languageName', ['code' => $code], 'languages');
            }, [
                'is_safe' => [ 'html' ]
            ]),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('locales', function () {
                return $this->requestStack->getMainRequest()->attributes->get('website')->getLocales();
            }, [
                 'is_safe' => [ 'html' ]
            ]),
            new TwigFunction('locale', function () {
                return $this->requestStack->getMainRequest()->attributes->get('website')->getLocale();
            }, [
                 'is_safe' => [ 'html' ]
            ]),
            new TwigFunction('current_website', function () {
                return $this->requestStack->getMainRequest()->attributes->get('website');
            }, [
                 'is_safe' => [ 'html' ]
            ]),
            new TwigFunction('website_list', function () {
                return $this->websiteRegistry->all();
            }, [
                'is_safe' => [ 'html' ]
            ]),
        ];
    }
}
