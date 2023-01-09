<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Cms\Menu;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tulia\Cms\Node\UserInterface\Web\Shared\Form\FormType\NodeTypeaheadType;
use Tulia\Cms\Taxonomy\UserInterface\Web\Shared\Form\FormType\TaxonomyTypeaheadType;

/**
 * @author Adam Banaszkiewicz
 */
class MenuItemSelectorForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('term_search_' . $options['taxonomy_type']->getCode(), TaxonomyTypeaheadType::class, [
            'label' => $options['taxonomy_type']->getName(),
            'translation_domain' => 'taxonomy',
            'locale' => $options['locale'],
            'website_id' => $options['website_id'],
            'taxonomy_type' => $options['taxonomy_type']->getCode(),
            'search_route_params' => [
                'taxonomy_type' => $options['taxonomy_type']->getCode(),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(['taxonomy_type', 'locale', 'website_id']);
    }
}
