<?php

declare(strict_types=1);

namespace Tulia\Cms\Tests\Behat\Taxonomy;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Tulia\Cms\Shared\Domain\WriteModel\Service\SlugGeneratorStrategy\SlugGeneratorStrategyInterface;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Event\TermCreated;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Event\TermDeleted;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Event\TermsHierarchyChanged;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Event\TermTranslated;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Event\TermUpdated;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Event\TermVisibilityChanged;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Taxonomy;
use Tulia\Cms\Tests\Behat\AggregateRootSpy;
use Tulia\Cms\Tests\Behat\Assert;
use Tulia\Cms\Tests\Helper\TestDoubles\Shared\Domain\WriteModel\Service\SlugGeneratorStrategy\PassthroughSluggeneratorStrategy;

/**
 * @author Adam Banaszkiewicz
 */
final class TaxonomyContext implements Context
{
    use TaxonomyBuildableTrait;

    private SlugGeneratorStrategyInterface $slugGeneratorStrategy;
    private string $locale = 'en_US';
    private string $defaultLocale = 'en_US';
    private string $term;

    public function __construct()
    {
        $this->slugGeneratorStrategy = new PassthroughSluggeneratorStrategy();
    }

    /**
     * @When I add new term :term to this taxonomy
     */
    public function iAddNewTermToThisTaxonomy(string $term): void
    {
        $this->taxonomy->addTerm($this->slugGeneratorStrategy, $term, $term, $term, $this->locales, $this->locale, $this->defaultLocale);
    }

    /**
     * @Then new term :term should be added
     */
    public function newTermShouldBeAdded(string $term): void
    {
        /** @var TermCreated $event */
        $event = $this->taxonomySpy->findEvent(TermCreated::class);

        Assert::assertInstanceOf(TermCreated::class, $event, 'Term should be added');
        Assert::assertSame($term, $event->termId);
    }

    /**
     * @Then new term should not be added
     */
    public function newTermShouldNotBeAdded(): void
    {
        throw new PendingException();
    }

    /**
     * @Given there is a term :term
     */
    public function thereIsATerm(string $term): void
    {
        $this->term = $term;
    }

    /**
     * @When I add new term :term to this taxonomy, as child of :parent
     */
    public function iAddNewTermToThisTaxonomyAsChildOf(string $term, string $parent): void
    {
        $this->taxonomy->addTerm($this->slugGeneratorStrategy, $term, $term, $term, $this->locales, $this->locale, $this->defaultLocale, parent: $parent);
    }

    /**
     * @Then new term should be child of :parent
     */
    public function newTermShouldBeChildOf(string $parent): void
    {
        /** @var TermCreated $event */
        $event = $this->taxonomySpy->findEvent(TermCreated::class);

        Assert::assertSame($parent, $event->parentId, 'Term should be child of another');
    }

    /**
     * @When I delete term :term
     */
    public function iDeleteTerm(string $term): void
    {
        $this->taxonomy->deleteTerm($term);
    }

    /**
     * @Then term :term should be deleted
     */
    public function termShouldBeDeleted(string $term): void
    {
        /** @var TermDeleted $event */
        $event = $this->taxonomySpy->findEvent(TermDeleted::class);

        Assert::assertInstanceOf(TermDeleted::class, $event, 'Term should be deleted');
        Assert::assertSame($term, $event->termId);
    }

    /**
     * @Then term should not be deleted
     */
    public function termShouldNotBeDeleted(): void
    {
        $event = $this->taxonomySpy->findEvent(TermDeleted::class);

        Assert::assertNull($event, 'Term should not be deleted');
    }

    /**
     * @Then new term should be root item
     */
    public function newTermShouldBeRootItem(): void
    {
        /** @var TermCreated $event */
        $event = $this->taxonomySpy->findEvent(TermCreated::class);

        Assert::assertNull($event->parentId, 'Term should be a root item');
    }

    /**
     * @When I update term :term with new name :newName
     */
    public function iUpdateTermWithNewName(string $term, string $newName): void
    {
        $this->taxonomy->updateTerm($this->slugGeneratorStrategy, $term, $this->locale, $newName, null, $this->defaultLocale);
    }

    /**
     * @Then term :term should be updated
     */
    public function termShouldBeUpdated(string $term): void
    {
        /** @var TermUpdated $event */
        $event = $this->taxonomySpy->findEvent(TermUpdated::class);

        Assert::assertInstanceOf(TermUpdated::class, $event, 'Term should be updated');
        Assert::assertSame($term, $event->termId);
    }

    /**
     * @When I update term :term with new name :newName, in :locale locale
     */
    public function iUpdateTermWithNewNameInLocale(string $term, string $newName, string $locale): void
    {
        $this->taxonomy->updateTerm($this->slugGeneratorStrategy, $term, $locale, $newName, null, $this->defaultLocale);
    }

    /**
     * @Then term :term should be translated in locale :locale
     */
    public function termShouldBeTranslatedInLocale(string $term, string $locale): void
    {
        /** @var TermTranslated $event */
        $event = $this->taxonomySpy->findEvent(TermTranslated::class);

        Assert::assertInstanceOf(TermTranslated::class, $event, 'Term should be translated');
        Assert::assertSame($term, $event->termId);
        Assert::assertSame($locale, $event->locale);
    }

    /**
     * @Then term :locale should not be translated
     */
    public function termShouldNotBeTranslated(string $locale): void
    {
        $event = $this->taxonomySpy->findEvent(TermTranslated::class);

        Assert::assertNull($event, 'Term should be not translated');
    }

    /**
     * @When I move term :term as child of :parent
     */
    public function iMoveTermAsChildOf(string $term, string $parent): void
    {
        $this->taxonomy->moveTermAsChildOf($term, $parent);
    }

    /**
     * @Then term :term should be moved as child of :parent
     */
    public function termShouldBeMovedAsChildOf(string $term, string $parent): void
    {
        /** @var TermsHierarchyChanged $event */
        $event = $this->taxonomySpy->findEvent(TermsHierarchyChanged::class);

        Assert::assertInstanceOf(TermsHierarchyChanged::class, $event, 'Terms hierarchy should be changed');
        Assert::assertTrue($event->isChildOf($term, $parent));
    }

    /**
     * @Then term :term should be a root
     */
    public function termShouldBeARoot(string $term): void
    {
        /** @var TermsHierarchyChanged $event */
        $event = $this->taxonomySpy->findEvent(TermsHierarchyChanged::class);

        Assert::assertInstanceOf(TermsHierarchyChanged::class, $event, 'Terms hierarchy should be changed');
        Assert::assertTrue($event->isRoot($term));
    }

    /**
     * @When I update terms hierarchy as the following :hierarchy
     */
    public function iUpdateTermsHierarchyAsTheFollowing(string $hierarchy): void
    {
        $parsedHierarchy = [];

        foreach (explode(';', $hierarchy) as $pair) {
            [$parent, $child] = explode(':', $pair);

            $parsedHierarchy[$child] = $parent;
        }

        $this->taxonomy->updateHierarchy($parsedHierarchy);
    }

    /**
     * @When I turn term :term visibility on, in :locale locale
     */
    public function iTurnTermVisibilityOnInLocale(string $term, string $locale): void
    {
        $this->taxonomy->turnTermVisibilityOn($term, $locale, $this->defaultLocale);
    }

    /**
     * @Then term :term should be invisible in locale :locale
     */
    public function termShouldBeInvisibleInLocale(string $term, string $locale): void
    {
        /** @var TermVisibilityChanged $event */
        $event = $this->taxonomySpy->findEvent(TermVisibilityChanged::class);

        Assert::assertInstanceOf(TermVisibilityChanged::class, $event, 'Term visibility should be changed');
        Assert::assertSame($term, $event->termId);
        Assert::assertFalse($event->isVisibleIn($locale));
    }

    /**
     * @When I turn term :term visibility off, in :arg2 locale
     */
    public function iTurnTermVisibilityOffInLocale(string $term, string $locale): void
    {
        $this->taxonomy->turnTermVisibilityOff($term, $locale, $this->defaultLocale);
    }

    /**
     * @Then term :term should be visible in locale :locale
     */
    public function termShouldBeVisibleInLocale(string $term, string $locale): void
    {
        /** @var TermVisibilityChanged $event */
        $event = $this->taxonomySpy->findEvent(TermVisibilityChanged::class);

        Assert::assertInstanceOf(TermVisibilityChanged::class, $event, 'Term visibility should be changed');
        Assert::assertSame($term, $event->termId);
        Assert::assertTrue($event->isVisibleIn($locale));
    }

    /**
     * @Then term :term visibility should not be changed
     */
    public function termVisibilityShouldNotBeChanged(string $term): void
    {
        $event = $this->taxonomySpy->findEvent(TermVisibilityChanged::class);

        Assert::assertNull($event, 'Term visibility should not be changed');
    }
}
