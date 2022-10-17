<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Twig\Extension;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteRegistryInterface;
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
        private readonly WebsiteInterface $website,
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
                return $this->website->getLocales();
            }, [
                 'is_safe' => [ 'html' ]
            ]),
            new TwigFunction('locale', function () {
                return $this->website->getLocale();
            }, [
                 'is_safe' => [ 'html' ]
            ]),
            new TwigFunction('current_website', function () {
                return $this->website;
            }, [
                 'is_safe' => [ 'html' ]
            ]),
            new TwigFunction('page_locale', function () {
                return $this->website->getLocale()->getCode();
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
