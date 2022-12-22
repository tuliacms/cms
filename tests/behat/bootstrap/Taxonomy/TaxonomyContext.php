<?php

declare(strict_types=1);

namespace Tulia\Cms\Tests\Behat\Taxonomy;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Tulia\Cms\Shared\Domain\WriteModel\Service\SlugGeneratorStrategy\SlugGeneratorStrategyInterface;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Event\TermCreated;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Event\TermDeleted;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Event\TermTranslated;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Event\TermUpdated;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Taxonomy;
use Tulia\Cms\Tests\Behat\AggregateRootSpy;
use Tulia\Cms\Tests\Behat\Assert;
use Tulia\Cms\Tests\Helper\TestDoubles\Shared\Domain\WriteModel\Service\SlugGeneratorStrategy\PassthroughSluggeneratorStrategy;

/**
 * @author Adam Banaszkiewicz
 */
final class TaxonomyContext implements Context
{
    private const WEBSITE_ID = 'a6eb5605-b37f-4c02-b684-9bc6614df31c';
    private Taxonomy $taxonomy;
    private AggregateRootSpy $taxonomySpy;
    private SlugGeneratorStrategyInterface $slugGeneratorStrategy;
    private array $locales = ['en_US'];
    private string $locale = 'en_US';
    private string $defaultLocale = 'en_US';
    private string $term;

    public function __construct()
    {
        $this->slugGeneratorStrategy = new PassthroughSluggeneratorStrategy();
    }

    /**
     * @Given there is a taxonomy :type
     */
    public function thereIsATaxonomy(string $type): void
    {
        $this->taxonomy = Taxonomy::create($type, self::WEBSITE_ID, $this->locales, $this->locale);
        $this->taxonomySpy = new AggregateRootSpy($this->taxonomy);
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
        $this->taxonomy->addTerm($this->slugGeneratorStrategy, $term, $term, $term, $this->locales, $this->locale, $this->defaultLocale);
        $this->taxonomy->collectDomainEvents();
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
     * @Given there are available locales :locales
     */
    public function thereAreAvailableLocales(string $locales): void
    {
        $this->locales = explode(',', $locales);
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
     * @Given this term is translated to :locale with name :name
     */
    public function thisTermIsTranslatedToWithName(string $locale, string $name): void
    {
        $this->taxonomy->updateTerm($this->slugGeneratorStrategy, $this->term, $locale, $name, null, $this->defaultLocale);
        $this->taxonomy->collectDomainEvents();
    }

    /**
     * @Then term :locale should not be translated
     */
    public function termShouldNotBeTranslated(string $locale): void
    {
        $event = $this->taxonomySpy->findEvent(TermTranslated::class);

        Assert::assertNull($event, 'Term should be not translated');
    }
}
