<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Cms\Menu;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tulia\Cms\Node\UserInterface\Web\Shared\Form\FormType\NodeTypeaheadType;

/**
 * @author Adam Banaszkiewicz
 */
class MenuItemSelectorForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('node_search_' . $options['node_type']->getCode(), NodeTypeaheadType::class, [
            'label' => $options['node_type']->getName(),
            'translation_domain' => 'node',
            'locale' => $options['locale'],
            'website_id' => $options['website_id'],
            'search_route_params' => [
                'node_type' => $options['node_type']->getCode(),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(['node_type', 'locale', 'website_id']);
    }
}
