<?php

declare(strict_types=1);

namespace Tulia\Cms\Tests\Behat\Node;

use Tulia\Cms\Node\Domain\WriteModel\Model\Node;
use Tulia\Cms\Node\Domain\WriteModel\Service\ParentTermsResolverInterface;
use Tulia\Cms\Tests\Behat\AggregateRootSpy;
use Tulia\Cms\Tests\Helper\ObjectMother\NodeMother;

/**
 * @property Node $node
 * @property AggregateRootSpy $nodeSpy
 * @property ParentTermsResolverInterface $parentTermsResolver
 * @author Adam Banaszkiewicz
 */
trait NodeBuildableTrait
{
    private NodeMother $nodeMother;

    public function __get(string $name)
    {
        if ($name !== 'node') {
            throw new \LogicException(sprintf('You can get only Node through magic getter, "%s" called.', $name));
        }

        if (false === property_exists($this, 'node')) {
            $this->node = $this->nodeMother->build($this->parentTermsResolver);
            $this->nodeSpy = new AggregateRootSpy($this->node);
        }

        return $this->node;
    }

    /**
     * @Given there is a node :node
     */
    public function thereIsANode(string $node): void
    {
        $this->nodeMother = NodeMother::aNode($node);
    }

    /**
     * @Given which is assigned to term :term of taxonomy :taxonomy
     */
    public function whichIsAssignedToTermOfTaxonomy(string $term, string $taxonomy): void
    {
        $this->nodeMother->withAssignationToTermOfTaxonomy($term, $taxonomy);
    }
}
