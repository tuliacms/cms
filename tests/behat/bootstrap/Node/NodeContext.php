<?php

declare(strict_types=1);

namespace Tulia\Cms\Tests\Behat\Node;

use Tulia\Cms\Node\Domain\WriteModel\Event\NodePublished;
use Tulia\Cms\Node\Domain\WriteModel\Event\TermsAssignationChanged;
use Tulia\Cms\Node\Domain\WriteModel\Service\ParentTermsResolverInterface;
use Tulia\Cms\Tests\Behat\Assert;
use Behat\Behat\Context\Context;
use Tulia\Cms\Node\Domain\WriteModel\Event\NodeDeleted;
use Tulia\Cms\Node\Domain\WriteModel\Event\PurposesUpdated;
use Tulia\Cms\Node\Domain\WriteModel\Exception\CannotDeleteNodeException;
use Tulia\Cms\Node\Domain\WriteModel\Exception\CannotImposePurposeToNodeException;
use Tulia\Cms\Node\Domain\WriteModel\Model\Node;
use Tulia\Cms\Node\Domain\WriteModel\Rules\CanAddPurpose\AlwaysTrueCanImposePurpose;
use Tulia\Cms\Node\Domain\WriteModel\Rules\CanAddPurpose\CanImposePurpose;
use Tulia\Cms\Node\Domain\WriteModel\Rules\CanDeleteNode\CanDeleteNode;
use Tulia\Cms\Node\Domain\WriteModel\Service\NodeByPurposeFinderInterface;
use Tulia\Cms\Node\Domain\WriteModel\Service\NodeChildrenDetectorInterface;
use Tulia\Cms\Node\Domain\WriteModel\Service\NodePurpose\NodePurposeRegistry;
use Tulia\Cms\Node\Domain\WriteModel\Service\NodePurpose\NodePurposeRegistryInterface;
use Tulia\Cms\Shared\Domain\WriteModel\Model\ValueObject\ImmutableDateTime;
use Tulia\Cms\Tests\Behat\AggregateRootSpy;
use Tulia\Cms\Tests\Behat\Node\TestDoubles\NodeByPurposeFinderStub;
use Tulia\Cms\Tests\Behat\Node\TestDoubles\NodeChildrenDetectorStub;
use Tulia\Cms\Tests\Behat\Node\TestDoubles\ParentTermsResolverStub;

/**
 * @author Adam Banaszkiewicz
 */
final class NodeContext implements Context
{
    use NodeBuildableTrait;

    private const WEBSITE_ID = 'ceb7a799-9491-4880-a50e-0c7f0dcba7f5';
    private const AUTHOR_ID = '9e8dc655-f476-4838-8c80-b22b1d401aab';
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    private AggregateRootSpy $nodeSpy;
    private ?string $exceptionReason = null;
    private array $availableLocales = ['en_US', 'pl_PL'];

    private NodeChildrenDetectorInterface $nodeChildrenDetector;
    private NodePurposeRegistryInterface $nodePurposeRegistry;
    private NodeByPurposeFinderInterface $nodeByPurposeFinder;
    private ParentTermsResolverInterface $parentTermsResolver;

    public function __construct()
    {
        $this->nodeChildrenDetector = new NodeChildrenDetectorStub();
        $this->nodePurposeRegistry = new NodePurposeRegistry([]);
        $this->nodeByPurposeFinder = new NodeByPurposeFinderStub();
        $this->parentTermsResolver = new ParentTermsResolverStub();
    }

    /**
     * @Given now is :datetime
     */
    public function nowIs(string $datetime): void
    {
        ImmutableDateTime::setTestNow($datetime);
    }

    /**
     * @When I create node :node
     */
    public function iCreateNode(string $node): void
    {
        $this->node = Node::create($node, 'page', self::WEBSITE_ID, self::AUTHOR_ID, $node, $this->availableLocales);
        $this->nodeSpy = new AggregateRootSpy($this->node);
    }

    /**
     * @Then node should be published to forever at :when
     */
    public function nodeShouldBePublishedAt(string $when): void
    {
        $event = $this->nodeSpy->findEvent(NodePublished::class);

        Assert::assertInstanceOf(NodePublished::class, $event, 'Node should be published');
        Assert::assertSame(
            date(self::DATE_FORMAT, strtotime($when)),
            $event->publishedAt->format(self::DATE_FORMAT)
        );
        Assert::assertTrue($event->isPublishedToForever());
    }

    /**
     * @When I publish this node at :when
     */
    public function iPublishThisNodeAt(string $when): void
    {
        $this->node->publish(new ImmutableDateTime($when));
    }

    /**
     * @When I publish this node at :from, to :to
     */
    public function iPublishThisNodeAtTo(string $from, string $to): void
    {
        $this->node->publish(new ImmutableDateTime($from), new ImmutableDateTime($to));
    }

    /**
     * @Then node should be published at :from to :to
     */
    public function nodeShouldBePublishedAtTo(string $from, string $to): void
    {
        $event = $this->nodeSpy->findEvent(NodePublished::class);

        Assert::assertInstanceOf(NodePublished::class, $event, 'Node should be published');
        Assert::assertSame(
            date(self::DATE_FORMAT, strtotime($from)),
            $event->publishedAt->format(self::DATE_FORMAT)
        );
        Assert::assertSame(
            date(self::DATE_FORMAT, strtotime($to)),
            $event->publishedTo->format(self::DATE_FORMAT)
        );
        Assert::assertFalse($event->isPublishedToForever());
    }

    /**
     * @Given there is node :child which is a child node of :parent
     */
    public function thereIsNodeWichIsAChildNodeOf(string $child, string $parent): void
    {
        $this->nodeChildrenDetector->makeNodeHasChildren($parent, $child);
    }

    /**
     * @When I delete this node
     */
    public function iDeleteThisNode(): void
    {
        try {
            $this->node->delete(new CanDeleteNode($this->nodeChildrenDetector));
        } catch (CannotDeleteNodeException $e) {
            $this->exceptionReason = $e->reason;
        }
    }

    /**
     * @Then this node should not be deleted, because: :reason
     */
    public function nodeShouldNotBeDeletedBecause(string $reason): void
    {
        $event = $this->nodeSpy->findEvent(NodeDeleted::class);

        Assert::assertNull($event, 'Node should not be deleted');
        Assert::assertSame($reason, $this->exceptionReason);
    }

    /**
     * @Then this node should be deleted
     */
    public function thisNodeShouldBeDeleted(): void
    {
        $event = $this->nodeSpy->findEvent(NodeDeleted::class);

        Assert::assertInstanceOf(NodeDeleted::class, $event, 'Node should be deleted');
    }

    /**
     * @Given there is a singular purpose :purpose
     */
    public function thereIsASingularPurpose(string $purpose): void
    {
        $this->nodePurposeRegistry->add($purpose, true);
    }

    /**
     * @Given there is a node with purpose :purpose
     */
    public function thereIsANodeWithPurpose(string $purpose): void
    {
        $this->nodeByPurposeFinder->makeOtherNodeHasSpecificPurpose($purpose);
    }

    /**
     * @When I impose purpose :purpose to this node
     */
    public function iImposePurposeToNode(string $purpose): void
    {
        try {
            $this->node->imposePurpose(
                new CanImposePurpose($this->nodePurposeRegistry, $this->nodeByPurposeFinder),
                $purpose
            );
        } catch (CannotImposePurposeToNodeException $e) {
            $this->exceptionReason = $e->reason;
        }
    }

    /**
     * @Then purpose should not be imposed to this node, becuase :reason
     */
    public function purposeShouldNotBeImposedToThisNode(string $reason): void
    {
        $event = $this->nodeSpy->findEvent(PurposesUpdated::class);

        Assert::assertNull($event, 'Purposes should not be updated');
        Assert::assertSame($reason, $this->exceptionReason);
    }

    /**
     * @Given there is a non-singular purpose named :purpose
     */
    public function thereIsANonSingularPurposeNamed(string $purpose): void
    {
        $this->nodePurposeRegistry->add($purpose, false);
    }

    /**
     * @Then purpose :purpose should be imposed to this node
     */
    public function purposeShouldBeImposedToThisNode(string $purpose): void
    {
        $event = $this->nodeSpy->findEvent(PurposesUpdated::class);

        Assert::assertInstanceOf(PurposesUpdated::class, $event, 'Purposes should be updated');
        Assert::assertContains($purpose, $event->purposes, 'Expected to has purpose in collection');
    }

    /**
     * @Then purpose should not be imposed to this node
     */
    public function purposeShouldNotBeImposedToThisNode2()
    {
        $event = $this->nodeSpy->findEvent(PurposesUpdated::class);

        Assert::assertNull($event, 'Purposes should not be updated');
    }

    /**
     * @Given this node has purpose named :purpose
     */
    public function thisNodeHasPurposeNamed(string $purpose): void
    {
        $this->node->imposePurpose(new AlwaysTrueCanImposePurpose(), $purpose);
        $this->node->collectDomainEvents();
    }

    /**
     * @When I persist purposes :purposes to this node
     */
    public function iPersistPurposesToThisNode(string $purposes): void
    {
        try {
            $this->node->persistPurposes(
                new CanImposePurpose($this->nodePurposeRegistry, $this->nodeByPurposeFinder),
                ...explode(',', $purposes)
            );
        } catch (CannotImposePurposeToNodeException $e) {
            $this->exceptionReason = $e->reason;
        }
    }

    /**
     * @Then this node should has updated purposes of :purposes
     */
    public function thisNodeShouldHasUpdatedPurposesOf(string $purposes): void
    {
        $event = $this->nodeSpy->findEvent(PurposesUpdated::class);

        Assert::assertInstanceOf(PurposesUpdated::class, $event, 'Purposes should be updated');
        Assert::assertSame($purposes, implode(',', $event->purposes), 'Expected to has exactly purposes in collection');
    }

    /**
     * @Given the category :term of taxonomy :taxonomy has parents of :parents
     */
    public function theCategoryOfTaxonomyHasParentsOf(string $taxonomy, string $term, string $parents): void
    {
        $this->parentTermsResolver->store($term, $taxonomy, $parents);
    }

    /**
     * @When I assign this node to main category :term of taxonomy :taxonomy
     */
    public function iAssignThisNodeToMainCategoryOfTaxonomy(string $term, string $taxonomy): void
    {
        $this->node->assignToMainCategory($this->parentTermsResolver, $term, $taxonomy);
    }

    /**
     * @When I assign this node to additional category :term of taxonomy :taxonomy
     */
    public function iAssignThisNodeToAdditionalCategoryOfTaxonomy(string $term, string $taxonomy): void
    {
        $this->node->assignToAdditionalCategory($this->parentTermsResolver, $term, $taxonomy);
    }

    /**
     * @Then this node should be assigned to :type category :term of taxonomy :taxonomy
     */
    public function thisNodeShouldBeAssignedToCategoryOfTaxonomy(string $term, string $taxonomy, string $type): void
    {
        /** @var TermsAssignationChanged $event */
        $event = $this->nodeSpy->findEvent(TermsAssignationChanged::class);

        Assert::assertInstanceOf(TermsAssignationChanged::class, $event, 'Terms assignation should be changed');
        Assert::assertTrue($event->isAssignedTo($term, $taxonomy, $type));
    }

    /**
     * @When I unassign this node from main category
     */
    public function iUnassignThisNodeFromMainCategory(): void
    {
        $this->node->unassignFromMainCategory($this->parentTermsResolver);
    }

    /**
     * @When I unassign this node from additional category :term of taxonomy :taxonomy
     */
    public function iUnassignThisNodeFromAdditionalCategoryOfTaxonomy(string $term, string $taxonomy): void
    {
        $this->node->unassignFromAdditionalCategory($this->parentTermsResolver, $term, $taxonomy);
    }


    /**
     * @Then this node should not be assigned to :type category :term of taxonomy :taxonomy
     */
    public function thisNodeShouldNotBeAssignedToTermOfTaxonomy(string $term, string $taxonomy, string $type): void
    {
        /** @var TermsAssignationChanged $event */
        $event = $this->nodeSpy->findEvent(TermsAssignationChanged::class);

        Assert::assertInstanceOf(TermsAssignationChanged::class, $event, 'Terms assignation should be changed');
        Assert::assertFalse($event->isAssignedTo($term, $taxonomy, $type));
    }

    /**
     * @Then assignation to categories for this node should not be changed
     */
    public function assignationToCategoriesForThisNodeShouldNotBeChanged(): void
    {
        $event = $this->nodeSpy->findEvent(TermsAssignationChanged::class);

        Assert::assertNull($event, 'Assignation to terms should not be changed');
    }

    /**
     * @When I replace additional categories assignations in this node to term :term of taxonomy :taxonomy
     */
    public function iReplaceAdditionalCategoriesAssignationsInThisNodeToTermOfTaxonomy(string $term, string $taxonomy): void
    {
        $this->node->persistAdditionalCategoriesAssignations($this->parentTermsResolver, [$term, $taxonomy]);
    }

    /**
     * @When I replace additional categories assignations in this node with empty collection
     */
    public function iReplaceAdditionalCategoriesAssignationsInThisNodeWithEmptyCollection()
    {
        $this->node->persistAdditionalCategoriesAssignations($this->parentTermsResolver, []);
    }
}
