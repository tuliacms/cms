<?php

declare(strict_types=1);

namespace Tulia\Cms\Tests\Behat\Content\Type;

use Assert;
use Behat\Behat\Context\Context;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Content\Type\Domain\WriteModel\Event\ContentTypeCreated;
use Tulia\Cms\Content\Type\Domain\WriteModel\Event\FieldAdded;
use Tulia\Cms\Content\Type\Domain\WriteModel\Event\FieldRemoved;
use Tulia\Cms\Content\Type\Domain\WriteModel\Event\FieldsGroupAdded;
use Tulia\Cms\Content\Type\Domain\WriteModel\Event\FieldsGroupRemoved;
use Tulia\Cms\Content\Type\Domain\WriteModel\Event\FieldsGroupRenamed;
use Tulia\Cms\Content\Type\Domain\WriteModel\Event\FieldsGroupsSorted;
use Tulia\Cms\Content\Type\Domain\WriteModel\Event\FieldsSorted;
use Tulia\Cms\Content\Type\Domain\WriteModel\Event\FieldUpdated;
use Tulia\Cms\Content\Type\Domain\WriteModel\Exception\ContentTypeCannotBeCreatedException;
use Tulia\Cms\Content\Type\Domain\WriteModel\Exception\FieldWithThatCodeAlreadyExistsException;
use Tulia\Cms\Content\Type\Domain\WriteModel\Exception\GroupWithCodeExistsException;
use Tulia\Cms\Content\Type\Domain\WriteModel\Model\ContentType;
use Tulia\Cms\Content\Type\Domain\WriteModel\Rules\CanCreateContentType;
use Tulia\Cms\Content\Type\Domain\WriteModel\Service\ContentTypeExistanceDetectorInterface;
use Tulia\Cms\Tests\Behat\AggregateRootSpy;
use Tulia\Cms\Tests\Behat\Content\Type\TestDoubles\AlwaysTrueCanCreateContentTypeStub;
use Tulia\Cms\Tests\Behat\Content\Type\TestDoubles\ContentTypeExistanceDetectorStub;
use Tulia\Cms\Tests\Behat\Content\Type\TestDoubles\ContentTypeRegistryStub;

/**
 * @author Adam Banaszkiewicz
 */
final class ContentTypeContext implements Context
{
    private ?ContentType $contentType = null;
    private AggregateRootSpy $contentTypeSpy;
    private ContentTypeRegistryInterface $contentTypeRegistry;
    private ContentTypeExistanceDetectorInterface $contentTypeExistanceDetector;

    public function __construct()
    {
        $this->contentTypeExistanceDetector = new ContentTypeExistanceDetectorStub('', false);
        $this->contentTypeRegistry = new ContentTypeRegistryStub(['node']);
    }

    /**
     * @When I creates ContentType named :name, with code :code, with type :type
     */
    public function iCreatesContenttypeNamedWithCodeWithType(string $name, string $code, string $type): void
    {
        try {
            $rules = new CanCreateContentType(
                $this->contentTypeRegistry,
                $this->contentTypeExistanceDetector
            );

            $this->contentType = ContentType::create($rules, $code, $type, $name);
            $this->contentTypeSpy = new AggregateRootSpy($this->contentType);
        } catch (ContentTypeCannotBeCreatedException $e) {
            // Do nothing. Assetions should be done in separate steps.
        }
    }

    /**
     * @Then new ContentType should not be created
     */
    public function newContenttypeShouldNotBeCreated(): void
    {
        Assert::assertNull($this->contentType, 'Content should not be created');
    }

    /**
     * @Given exists ContentType named :name, with code :code, with type :type
     */
    public function existsContenttypeNamedWithCodeWithType(string $name, string $code, string $type): void
    {
        $this->contentTypeExistanceDetector = new ContentTypeExistanceDetectorStub($code, true);
    }

    /**
     * @Then new ContentType should be created
     */
    public function newContenttypeShouldBeCreated(): void
    {
        $event = $this->contentTypeSpy->findEvent(ContentTypeCreated::class);

        Assert::assertInstanceOf(ContentTypeCreated::class, $event);
    }

    /**
     * @Given I have ContentType named :name, with code :code, with type :type
     */
    public function iHaveContenttypeNamedWithCodeWithType(string $name, string $code, string $type): void
    {
        $rules = new AlwaysTrueCanCreateContentTypeStub();

        $this->contentType = ContentType::create($rules, $code, $type, $name);
        $this->contentTypeSpy = new AggregateRootSpy($this->contentType);
    }

    /**
     * @When I adds new field named :name, with code :code, of type :type, to group :group
     */
    public function iAddsNewFieldNamedWithCodeOfTypeToGroup(string $name, string $code, string $type, string $group): void
    {
        try {
            $this->contentType->addFieldToGroup($group, $code, $type, $name);
        } catch (FieldWithThatCodeAlreadyExistsException $e) {
            // Do nothing. Assetions should be done in separate steps.
        }
    }

    /**
     * @Then field should not be added
     */
    public function fieldShouldNotBeAdded(): void
    {
        $event = $this->contentTypeSpy->findEvent(FieldAdded::class);

        Assert::assertNull($event, 'New field should not be added');
    }

    /**
     * @Given there is a fields group in this ContentType, named :name with code :code for section :section
     */
    public function thereIsAFieldsGroupInThisContenttypeNamedWithCodeForSection(string $name, string $code, string $section): void
    {
        $this->contentType->addFieldsGroup($code, $name, $section);
        // Clear from the events to prevent fals epositived in fields tests
        $this->contentType->collectDomainEvents();
    }

    /**
     * @When I adds new field named :name, with code :code, of type :type, to group :group, for parent :parent
     */
    public function iAddsNewFieldNamedWithCodeOfTypeToGroupForParent(string $name, string $code, string $type, string $group, string $parent): void
    {
        $this->contentType->addFieldToGroup($group, $code, $type, $name, [], [], [], $parent);
    }

    /**
     * @Given there is a field named :name, with code :code, of type :type, to group :group
     */
    public function thereIsAFieldNamedWithCodeOfTypeToGroup(string $name, string $code, string $type, string $group): void
    {
        $this->contentType->addFieldToGroup($group, $code, $type, $name);
        // Clear from the events to prevent fals epositived in fields tests
        $this->contentType->collectDomainEvents();
    }

    /**
     * @Then field should be added
     */
    public function fieldShouldBeAdded(): void
    {
        $event = $this->contentTypeSpy->findEvent(FieldAdded::class);

        Assert::assertInstanceOf(FieldAdded::class, $event, 'New field should be added');
    }

    /**
     * @When I updates field :code with name :name
     */
    public function iUpdatesFieldWithName(string $code, string $name): void
    {
        $this->contentType->updateField($code, $name);
    }

    /**
     * @Then field should not be updated
     */
    public function fieldShouldNotBeUpdated(): void
    {
        $event = $this->contentTypeSpy->findEvent(FieldUpdated::class);

        Assert::assertNull($event, 'Field should not be updated');
    }

    /**
     * @Then field should be updated
     */
    public function fieldShouldBeUpdated(): void
    {
        $event = $this->contentTypeSpy->findEvent(FieldUpdated::class);

        Assert::assertInstanceOf(FieldUpdated::class, $event, 'Field should be updated');
    }

    /**
     * @When I removes field :code
     */
    public function iRemovesField(string $code): void
    {
        $this->contentType->removeField($code);
    }

    /**
     * @Then field should not be removed
     */
    public function fieldShouldNotBeRemoved(): void
    {
        $event = $this->contentTypeSpy->findEvent(FieldRemoved::class);

        Assert::assertNull($event, 'Field should not be removed');
    }

    /**
     * @Then field should be removed
     */
    public function fieldShouldBeRemoved(): void
    {
        $event = $this->contentTypeSpy->findEvent(FieldRemoved::class);

        Assert::assertInstanceOf(FieldRemoved::class, $event, 'Field should be removed');
    }

    /**
     * @When I sort fields to new order :newOrder
     */
    public function iSortFieldsToNewOrder(string $newOrder): void
    {
        $this->contentType->sortFields(explode(',', $newOrder));
    }

    /**
     * @Then fields should be in order :expectedOrder
     */
    public function fieldsShouldBeInOrder(string $expectedOrder): void
    {
        $event = $this->contentTypeSpy->findEvent(FieldsSorted::class);

        Assert::assertInstanceOf(FieldsSorted::class, $event, 'Fields should be sorted');
        Assert::assertSame(explode(',', $expectedOrder), $event->getNewPositions(), 'Fields was not sorted as expected');
    }

    /**
     * @When I adds new fields group named :name with code :code for section :section
     */
    public function iAddsNewFieldsGroupNamedWithCodeForSection(string $name, string $code, string $section): void
    {
        try {
            $this->contentType->addFieldsGroup($code, $name, $section);
        } catch (GroupWithCodeExistsException $e) {
            // Do nothing. Assetions should be done in separate steps.
        }
    }

    /**
     * @Then fields group should not be added
     */
    public function fieldsGroupShouldNotBeAdded(): void
    {
        $event = $this->contentTypeSpy->findEvent(FieldsGroupAdded::class);

        Assert::assertNull($event, 'Fields group should not be added');
    }

    /**
     * @Then fields group should be added
     */
    public function fieldsGroupShouldBeAdded(): void
    {
        $event = $this->contentTypeSpy->findEvent(FieldsGroupAdded::class);

        Assert::assertInstanceOf(FieldsGroupAdded::class, $event, 'Fields group should be added');
    }

    /**
     * @When I sort fields groups to new order :order
     */
    public function iSortFieldsGroupsToNewOrder(string $newOrder): void
    {
        $this->contentType->sortFieldsGroups(explode(',', $newOrder));
    }

    /**
     * @Then fields groups should be in order :expectedOrder
     */
    public function fieldsGroupsShouldBeInOrder(string $expectedOrder): void
    {
        $event = $this->contentTypeSpy->findEvent(FieldsGroupsSorted::class);

        Assert::assertInstanceOf(FieldsGroupsSorted::class, $event, 'Fields groups should be sorted');
        Assert::assertSame(explode(',', $expectedOrder), $event->getNewPositions(), 'Fields groups was not sorted as expected');
    }

    /**
     * @When I remove fields group with code :code
     */
    public function iRemoveFieldsGroupWithCode(string $code): void
    {
        $this->contentType->removeFieldsGroup($code);
    }

    /**
     * @Then fields group should not be removed
     */
    public function fieldsGroupShouldNotBeRemoved(): void
    {
        $event = $this->contentTypeSpy->findEvent(FieldsGroupRemoved::class);

        Assert::assertNull($event, 'Fields group should not be removed');
    }

    /**
     * @Then fields group should be removed
     */
    public function fieldsGroupShouldBeRemoved(): void
    {
        $event = $this->contentTypeSpy->findEvent(FieldsGroupRemoved::class);

        Assert::assertInstanceOf(FieldsGroupRemoved::class, $event, 'Fields groups should be removed');
    }

    /**
     * @When I rename fields group with code :code to :newName
     */
    public function iRenameFieldsGroupWithCodeTo(string $code, string $newName): void
    {
        $this->contentType->renameFieldsGroup($code, $newName);
    }

    /**
     * @Then fields group :code should be renamed to :expectedName
     */
    public function fieldsGroupShouldBeRenamedTo($code, $expectedName): void
    {
        $event = $this->contentTypeSpy->findEvent(FieldsGroupRenamed::class);

        Assert::assertInstanceOf(FieldsGroupRenamed::class, $event, 'Fields groups should be renamed');
        Assert::assertSame($code, $event->getGroupCode(), 'Renamed fields group is not as expected');
        Assert::assertSame($expectedName, $event->getName(), 'Fields group name is not as extected');
    }
}
