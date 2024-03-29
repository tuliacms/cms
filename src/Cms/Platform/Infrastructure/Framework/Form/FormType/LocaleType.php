<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class LocaleType extends AbstractType
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly array $availableTranslations,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $locales = [];

        foreach ($this->availableTranslations as $locale) {
            $locales[$this->translator->trans('languageName', [ 'code' => $locale ], 'languages')] = $locale;
        }

        $resolver->setDefault('choices', $locales);
        $resolver->setDefault('constraints', [
            new Assert\Choice(['choices' => $locales]),
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
