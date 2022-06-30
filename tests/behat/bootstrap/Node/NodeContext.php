<?php

declare(strict_types=1);

namespace Tulia\Cms\Tests\Behat\Node;

use Assert;
use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Tulia\Cms\Content\Attributes\Domain\WriteModel\Model\Attribute;
use Tulia\Cms\Node\Domain\WriteModel\Event\NodeDeleted;
use Tulia\Cms\Node\Domain\WriteModel\Event\PurposesUpdated;
use Tulia\Cms\Node\Domain\WriteModel\Exception\CannotDeleteNodeException;
use Tulia\Cms\Node\Domain\WriteModel\Exception\CannotImposePurposeToNodeException;
use Tulia\Cms\Node\Domain\WriteModel\Model\Node;
use Tulia\Cms\Node\Domain\WriteModel\Model\ValueObject\Author;
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

/**
 * @author Adam Banaszkiewicz
 */
final class NodeContext implements Context
{
    private const WEBSITE_ID = 'ceb7a799-9491-4880-a50e-0c7f0dcba7f5';
    private const AUTHOR_ID = '9e8dc655-f476-4838-8c80-b22b1d401aab';
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    private Node $node;
    private AggregateRootSpy $nodeSpy;
    private ?string $exceptionReason = null;

    private NodeChildrenDetectorInterface $nodeChildrenDetector;
    private NodePurposeRegistryInterface $nodePurposeRegistry;
    private NodeByPurposeFinderInterface $nodeByPurposeFinder;

    public function __construct()
    {
        $this->nodeChildrenDetector = new NodeChildrenDetectorStub();
        $this->nodePurposeRegistry = new NodePurposeRegistry([]);
        $this->nodeByPurposeFinder = new NodeByPurposeFinderStub();
    }

    /**
     * @Given there is a node :node
     */
    public function thereIsANode(string $node): void
    {
        $this->node = Node::createNew($node, 'page', self::WEBSITE_ID, 'en_US', new Author(self::AUTHOR_ID));
        $this->nodeSpy = new AggregateRootSpy($this->node);
    }

    /**
     * @Given this node has attribute :uri with value :value
     */
    public function nodeHasAttributeWithValue(string $uri, string $value): void
    {
        $this->node->addAttribute($this->attribute($uri, $value));
    }

    /**
     * @When admin removes attribute :uri from this node
     */
    public function adminRemovesAttribute(string $uri): void
    {
        $this->node->removeAttribute($uri);
    }

    /**
     * @Then this node don't have attribute :uri
     */
    public function nodeDontHaveAttribute(string $uri): void
    {
        $this->node->removeAttribute($uri);
        $this->node->collectDomainEvents();
    }

    /**
     * @When admin adds attribute :uri with value :value to this node
     */
    public function adminAddsAttributeWithValue(string $uri, string $value): void
    {
        $this->node->addAttribute($this->attribute($uri, $value));
    }

    /**
     * @Then node should have attribute :uri with value :value
     */
    public function nodeShouldHaveAttributeWithValue(string $uri, string $value): void
    {
        Assert::assertTrue($this->node->hasAttribute($uri));
        Assert::assertSame($value, $this->node->getAttribute($uri)->getValue());
    }

    /**
     * @Then this node should not have attribute :uri
     */
    public function thisNodeShouldNotHaveAttribute(string $uri): void
    {
        Assert::assertFalse($this->node->hasAttribute($uri));
    }

    /**
     * @Then node have attribute :uri
     */
    public function nodeHaveAttribute(string $uri): void
    {
        Assert::assertTrue($this->node->hasAttribute($uri));
    }

    /**
     * @Then node should be published at :when
     */
    public function nodeShouldBePublishedAt(string $when): void
    {
        Assert::assertSame(
            date(self::DATE_FORMAT, strtotime($when)),
            $this->node->getPublishedAt()->format(self::DATE_FORMAT)
        );
    }

    /**
     * @When admin change published date to :date
     */
    public function adminChangePublishedDateTo(string $date): void
    {
        $this->node->publishNodeAt(new ImmutableDateTime($date));
    }

    /**
     * @Then node should be published forever
     */
    public function nodeShouldBePublishedForever(): void
    {
        Assert::assertNull($this->node->getPublishedTo());
    }

    /**
     * @When admin change published end date to :date
     */
    public function adminChangePublishedEndDateTo(string $date): void
    {
        $this->node->publishNodeTo(new ImmutableDateTime($date));
    }

    /**
     * @Then node is published to :when
     */
    public function nodeIsPublishedTo(string $when): void
    {
        Assert::assertSame(
            date(self::DATE_FORMAT, strtotime($when)),
            $this->node->getPublishedTo()->format(self::DATE_FORMAT)
        );
    }

    /**
     * @When admin change node to published forever
     */
    public function adminChangeNodeToPublishedForever(): void
    {
        $this->node->publishNodeForever();
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
        $this->nodeByPurposeFinder->makeOtherNodeHasSpecificPurpose($purpose, self::WEBSITE_ID);
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

    private function attribute(string $uri, string $value): Attribute
    {
        return new Attribute($uri, $uri, $value, $value, [], []);
    }
}
