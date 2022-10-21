<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\UserInterface\Web\Form\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Website\Domain\WriteModel\Service\LocaleStorageInterface;

/**
 * @author Adam Banaszkiewicz
 */
class LocaleChoiceType extends AbstractType
{
    public function __construct(
        private readonly LocaleStorageInterface $storage,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $source = $this->storage->all();

        $locales = [];

        foreach ($source as $locale) {
            $locales[$this->translator->trans('languageName', [ 'code' => $locale->getCode() ], 'languages') . ' [' . $locale->getCode() . ']'] = $locale->getCode();
        }

        $resolver->setDefaults([
            'choices' => $locales,
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
