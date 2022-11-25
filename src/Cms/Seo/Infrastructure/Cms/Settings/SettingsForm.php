<?php

declare(strict_types=1);

namespace Tulia\Cms\Seo\Infrastructure\Cms\Settings;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\AbstractType;

/**
 * @author Adam Banaszkiewicz
 */
class SettingsForm extends AbstractType
{
    public function __construct(
        private readonly array $robotsOptionsList,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('seo_global_robots', Type\ChoiceType::class, [
                'label' => 'metaRobots',
                'help' => 'metaRobotsGlobalHelp',
                'translation_domain' => 'seo',
                'choices' => array_flip($this->robotsOptionsList),
                /*'multiple' => true,*/
                'constraints' => [
                    new Assert\Choice([ 'choices' => array_flip($this->robotsOptionsList)/*, 'multiple' => true*/ ]),
                ],
            ]);
    }
}
