<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Twig\Extension;

use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\Packages;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\User\Application\Service\AuthenticatedUserProviderInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class TranslatorExtension extends AbstractExtension
{
    private array $translationScripts = [
        'pl_PL' => 'pl.trans.js',
        'en_US' => 'en.trans.js',
    ];

    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly AuthenticatedUserProviderInterface $authenticatedUserProvider,
        private readonly Packages $packages,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('trans_exists', function (string $id, array $parameters = [], string $domain = null, string $locale = null) {
                return $id !== $this->translator->trans($id, $parameters, $domain, $locale);
            }),
            new TwigFunction('frontend_translations_script', function () {
                $locale = $this->authenticatedUserProvider->getUser()->getLocale();

                if (isset($this->translationScripts[$locale])) {
                    $filename = $this->translationScripts[$locale];
                } else {
                    $filename = $this->translationScripts['en_US'];
                }

                return $this->packages->getUrl("/assets/core/backend/theme/translations/{$filename}");
            }),
        ];
    }
}
