<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\UserInterface\Web\Shared\Form\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType\TypeaheadType;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Finder\TermFinderInterface;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Finder\TermFinderScopeEnum;

/**
 * @author Adam Banaszkiewicz
 */
class TaxonomyTypeaheadType extends AbstractType
{
    public function __construct(
        private readonly TermFinderInterface $termFinder,
        private readonly RouterInterface $router,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'search_route'  => 'backend.term.search.typeahead',
            'display_prop'  => 'name',
            'data_provider_single' => function (array $criteria): ?array {
                $term = $this->termFinder->findOne([
                    'id' => $criteria['value'],
                    'locale' => $criteria['locale'],
                    'website_id' => $criteria['website_id'],
                ], TermFinderScopeEnum::INTERNAL);

                return $term ? ['name' => $term->getName()] : null;
            },
        ]);

        $resolver->setRequired(['taxonomy_type']);
    }

    public function getParent(): string
    {
        return TypeaheadType::class;
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $options['search_route_params'] = array_merge(
            $options['search_route_params'],
            [ 'taxonomy_type' => $options['taxonomy_type'] ]
        );

        $view->vars['typeahead_url'] = $this->router->generate(
            $options['search_route'],
            $options['search_route_params']
        );
    }
}
