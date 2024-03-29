<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UserInterface\Web\Shared\Form\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tulia\Cms\Node\Domain\ReadModel\Finder\NodeFinderInterface;
use Tulia\Cms\Node\Domain\ReadModel\Finder\NodeFinderScopeEnum;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType\TypeaheadType;

/**
 * @author Adam Banaszkiewicz
 */
class NodeTypeaheadType extends AbstractType
{
    protected NodeFinderInterface $nodeFinder;

    public function __construct(NodeFinderInterface $nodeFinder)
    {
        $this->nodeFinder = $nodeFinder;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'search_route'  => 'backend.node.search.typeahead',
            'display_prop'  => 'title',
            'data_provider_single' => function (array $criteria): ?array {
                $node = $this->nodeFinder->findOne([
                    'id' => $criteria['value'],
                    'locale' => $criteria['locale'],
                    'website_id' => $criteria['website_id'],
                ], NodeFinderScopeEnum::INTERNAL);

                return $node ? ['id' => $node->getId(), 'title' => $node->getTitle()] : null;
            },
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return TypeaheadType::class;
    }
}
