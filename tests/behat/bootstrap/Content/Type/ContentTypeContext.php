<?php

declare(strict_types=1);

namespace Tulia\Cms\Tests\Behat\Content\Type;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Content\Type\Domain\WriteModel\Model\ContentType;
use Tulia\Cms\Content\Type\Domain\WriteModel\Specification\CreateContentType\CreateContentTypeSpecification;
use Tulia\Cms\Tests\Behat\AggregateRootSpy;
use Tulia\Cms\Tests\Behat\Content\Type\TestDoubles\ContentTypeRegistryMock;

/**
 * @author Adam Banaszkiewicz
 */
final class ContentTypeContext implements Context
{
    private ?ContentType $contentType = null;
    private AggregateRootSpy $contentTypeSpy;
    private ContentTypeRegistryInterface $contentTypeRegistry;

    public function __construct()
    {
        $this->contentTypeRegistry = new ContentTypeRegistryMock(['node']);
    }

    /**
     * @When I creates ContentType named :name, with code :code, with type :type
     */
    public function iCreatesContenttypeNamedWithCodeWithType(string $name, string $code, string $type): void
    {
        $spec = new CreateContentTypeSpecification($this->contentTypeRegistry);

        $this->contentType = ContentType::create($spec, $code, $type, $name);
        $this->contentTypeSpy = new AggregateRootSpy($this->contentType);
    }

    /**
     * @Then new ContentType should not be created
     */
    public function newContenttypeShouldNotBeCreated(): void
    {
        \Assert::assertNull($this->contentType, 'Content should not be created');
    }

    /**
     * @Given exists ContentType named :arg1, with code :arg2, with type :arg3
     */
    public function existsContenttypeNamedWithCodeWithType($arg1, $arg2, $arg3): void
    {
        throw new PendingException();
    }

    /**
     * @Then new ContentType should be created
     */
    public function newContenttypeShouldBeCreated(): void
    {
        throw new PendingException();
    }

    /**
     * @Given I have ContentType named :arg1, with code :arg2, with type :arg3
     */
    public function iHaveContenttypeNamedWithCodeWithType($arg1, $arg2, $arg3): void
    {
        throw new PendingException();
    }

    /**
     * @When I adds new field named :arg1, with code :arg2, of type :arg3, to group :arg4
     */
    public function iAddsNewFieldNamedWithCodeOfTypeToGroup($arg1, $arg2, $arg3, $arg4): void
    {
        throw new PendingException();
    }

    /**
     * @Then field should not be added
     */
    public function fieldShouldNotBeAdded(): void
    {
        throw new PendingException();
    }

    /**
     * @Given there is a fields group in this ContentType, named :arg1 with code :arg2 for section :arg3
     */
    public function thereIsAFieldsGroupInThisContenttypeNamedWithCodeForSection($arg1, $arg2, $arg3): void
    {
        throw new PendingException();
    }

    /**
     * @When I adds new field named :arg1, with code :arg2, of type :arg3, to group :arg4, for parent :arg5
     */
    public function iAddsNewFieldNamedWithCodeOfTypeToGroupForParent($arg1, $arg2, $arg3, $arg4, $arg5): void
    {
        throw new PendingException();
    }

    /**
     * @Given there is a field named :arg1, with code :arg2, of type :arg3, to group :arg4
     */
    public function thereIsAFieldNamedWithCodeOfTypeToGroup($arg1, $arg2, $arg3, $arg4): void
    {
        throw new PendingException();
    }

    /**
     * @Then field should be added
     */
    public function fieldShouldBeAdded(): void
    {
        throw new PendingException();
    }

    /**
     * @When I updates field :arg1 with name :arg2
     */
    public function iUpdatesFieldWithName($arg1, $arg2): void
    {
        throw new PendingException();
    }

    /**
     * @Then field should not be updated
     */
    public function fieldShouldNotBeUpdated(): void
    {
        throw new PendingException();
    }

    /**
     * @Then field should be updated
     */
    public function fieldShouldBeUpdated(): void
    {
        throw new PendingException();
    }

    /**
     * @When I removes field :arg1
     */
    public function iRemovesField($arg1): void
    {
        throw new PendingException();
    }

    /**
     * @Then field should not be removed
     */
    public function fieldShouldNotBeRemoved(): void
    {
        throw new PendingException();
    }

    /**
     * @Then field should be removed
     */
    public function fieldShouldBeRemoved(): void
    {
        throw new PendingException();
    }

    /**
     * @When I sort fields to new order :arg1
     */
    public function iSortFieldsToNewOrder($arg1): void
    {
        throw new PendingException();
    }

    /**
     * @Then fields should be in order :arg1
     */
    public function fieldsShouldBeInOrder($arg1): void
    {
        throw new PendingException();
    }

    /**
     * @When I adds new fields group named :arg1 with code :arg2 for section :arg3
     */
    public function iAddsNewFieldsGroupNamedWithCodeForSection($arg1, $arg2, $arg3): void
    {
        throw new PendingException();
    }

    /**
     * @Then fields group should not be added
     */
    public function fieldsGroupShouldNotBeAdded(): void
    {
        throw new PendingException();
    }

    /**
     * @Then fields group should be added
     */
    public function fieldsGroupShouldBeAdded(): void
    {
        throw new PendingException();
    }

    /**
     * @When I sort fields groups to new order :arg1
     */
    public function iSortFieldsGroupsToNewOrder($arg1): void
    {
        throw new PendingException();
    }

    /**
     * @Then fields groups should be in order :arg1
     */
    public function fieldsGroupsShouldBeInOrder($arg1): void
    {
        throw new PendingException();
    }

    /**
     * @When I remove fields group with code :arg1
     */
    public function iRemoveFieldsGroupWithCode($arg1): void
    {
        throw new PendingException();
    }

    /**
     * @Then fields group should not be removed
     */
    public function fieldsGroupShouldNotBeRemoved(): void
    {
        throw new PendingException();
    }

    /**
     * @Then fields group should be removed
     */
    public function fieldsGroupShouldBeRemoved(): void
    {
        throw new PendingException();
    }

    /**
     * @When I rename fields group with code :arg1 to :arg2
     */
    public function iRenameFieldsGroupWithCodeTo($arg1, $arg2): void
    {
        throw new PendingException();
    }

    /**
     * @Then fields group :arg1 should be renamed to :arg2
     */
    public function fieldsGroupShouldBeRenamedTo($arg1, $arg2): void
    {
        throw new PendingException();
    }
}
