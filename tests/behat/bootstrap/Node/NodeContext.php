<?php

namespace Node;

use Assert;
use Behat\Behat\Context\Context;
use Tulia\Cms\Content\Attributes\Domain\WriteModel\Model\Attribute;
use Tulia\Cms\Node\Domain\WriteModel\Model\Node;
use Tulia\Cms\Shared\Domain\WriteModel\Model\ValueObject\ImmutableDateTime;

/**
 * Defines application features from the specific context.
 */
class NodeContext implements Context
{
    private const NODE_ID = 'b43b49dc-dc9a-416f-8bd6-60e8c1a7af76';
    private const WEBSITE_ID = 'ceb7a799-9491-4880-a50e-0c7f0dcba7f5';
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    private Node $node;

    /**
     * @Given there is a node
     */
    public function thereIsANode(): void
    {
        $this->node = Node::createNew(self::NODE_ID, 'page', self::WEBSITE_ID, 'en_US');
    }

    /**
     * @Given node has attribute :uri with value :value
     */
    public function nodeHasAttributeWithValue($uri, $value): void
    {
        $this->node->addAttribute($this->attribute($uri, $value));
    }

    /**
     * @When admin removes attribute :uri
     */
    public function adminRemovesAttribute($uri): void
    {
        $this->node->removeAttribute($uri);
    }

    /**
     * @Then node dont have attribute :uri
     */
    public function nodeDontHaveAttribute($uri)
    {
        Assert::assertFalse($this->node->hasAttribute($uri));
    }

    /**
     * @When admin adds attribute :uri with value :value
     */
    public function adminAddsAttributeWithValue($uri, $value): void
    {
        $this->node->addAttribute($this->attribute($uri, $value));
    }

    /**
     * @Then node have attribute :uri with value :value
     */
    public function nodeHaveAttributeWithValue($uri, $value): void
    {
        Assert::assertTrue($this->node->hasAttribute($uri));
        Assert::assertSame($value, $this->node->getAttribute($uri)->getValue());
    }

    /**
     * @Then node have attribute :uri
     */
    public function nodeHaveAttribute($uri): void
    {
        Assert::assertTrue($this->node->hasAttribute($uri));
    }

    /**
     * @Then node is published at :when
     */
    public function nodeIsPublishedAt($when): void
    {
        Assert::assertSame(
            date(self::DATE_FORMAT, strtotime($when)),
            $this->node->getPublishedAt()->format(self::DATE_FORMAT)
        );
    }

    /**
     * @When admin change published date to :date
     */
    public function adminChangePublishedDateTo($date): void
    {
        $this->node->setPublishedAt(new ImmutableDateTime($date));
    }

    /**
     * @Then node is published forever
     */
    public function nodeIsPublishedForever(): void
    {
        Assert::assertNull($this->node->getPublishedTo());
    }

    /**
     * @When admin change published end date to :date
     */
    public function adminChangePublishedEndDateTo($date): void
    {
        $this->node->setPublishedTo(new ImmutableDateTime($date));
    }

    /**
     * @Then node is published to :when
     */
    public function nodeIsPublishedTo($when): void
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
        $this->node->setPublishedToForever();
    }

    private function attribute(string $uri, string $value): Attribute
    {
        return new Attribute($uri, $uri, $value, $value, [], [], false, false);
    }
}
