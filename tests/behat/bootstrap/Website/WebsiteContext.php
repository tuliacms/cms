<?php

declare(strict_types=1);

namespace Tulia\Cms\Tests\Behat\Website;

use Behat\Behat\Context\Context;
use Symfony\Component\Uid\Uuid;
use Tulia\Cms\Tests\Behat\AggregateRootSpy;
use Tulia\Cms\Tests\Behat\Assert;
use Tulia\Cms\Tests\Behat\Website\TestDoubles\StubCurrentWebsiteProvider;
use Tulia\Cms\Tests\Behat\Website\TestDoubles\StubWebsitesCounterQuery;
use Tulia\Cms\Website\Domain\WriteModel\Event\LocaleActivityChanged;
use Tulia\Cms\Website\Domain\WriteModel\Event\LocaleAdded;
use Tulia\Cms\Website\Domain\WriteModel\Event\LocaleDeleted;
use Tulia\Cms\Website\Domain\WriteModel\Event\WebsiteActivityChanged;
use Tulia\Cms\Website\Domain\WriteModel\Event\WebsiteCreated;
use Tulia\Cms\Website\Domain\WriteModel\Event\WebsiteDeleted;
use Tulia\Cms\Website\Domain\WriteModel\Event\WebsiteUpdated;
use Tulia\Cms\Website\Domain\WriteModel\Exception\CannotAddLocaleException;
use Tulia\Cms\Website\Domain\WriteModel\Exception\CannotDeleteLocaleException;
use Tulia\Cms\Website\Domain\WriteModel\Exception\CannotDeleteWebsiteException;
use Tulia\Cms\Website\Domain\WriteModel\Model\Website;
use Tulia\Cms\Website\Domain\WriteModel\Query\WebsitesCounterQueryInterface;
use Tulia\Cms\Website\Domain\WriteModel\Rules\CanDeleteLocale\CanDeleteLocale;
use Tulia\Cms\Website\Domain\WriteModel\Rules\CanDeleteWebsite\CanDeleteWebsite;
use Tulia\Cms\Website\Domain\WriteModel\Rules\CanAddLocale\CanAddLocale;

/**
 * @author Adam Banaszkiewicz
 */
final class WebsiteContext implements Context
{
    use WebsiteBuildableTrait;

    private ?AggregateRootSpy $websiteSpy = null;
    private WebsitesCounterQueryInterface $websitesCounterQuery;
    private StubCurrentWebsiteProvider $currentWebsitePrivider;
    private ?string $reasonWhyCannotAddLocale = null;
    private ?string $reasonWhyCannotDeleteWebsite = null;

    public function __construct()
    {
        $this->websitesCounterQuery = new StubWebsitesCounterQuery();
        $this->currentWebsitePrivider = new StubCurrentWebsiteProvider();
    }

    /**
     * @When I create website named :name, with default locale :code
     */
    public function iCreateWebsiteNamedWithDefaultLocale(string $name, string $code): void
    {
        $this->website = Website::create(
            id: $this->id(),
            name: $name,
            localeCode: $code,
            domain: 'localhost',
        );
        $this->websiteSpy = new AggregateRootSpy($this->website);
    }

    /**
     * @Then new website should be created
     */
    public function newWebsiteShouldBeCreated(): void
    {
        $event = $this->websiteSpy->findEvent(WebsiteCreated::class);

        Assert::assertInstanceOf(WebsiteCreated::class, $event, 'New website should be created');
    }

    /**
     * @When I add locale :code to this website
     */
    public function iAddLocaleToThisWebsite(string $code): void
    {
        try {
            $this->website->addLocale(new CanAddLocale(), $code);
        } catch (CannotAddLocaleException $e) {
            $this->reasonWhyCannotAddLocale = $e->reason;
        }
    }

    /**
     * @Then new locale should not be added because :reason
     */
    public function newLocaleShouldNotBeAddedBecause(string $reason): void
    {
        $event = $this->websiteSpy->findEvent(LocaleAdded::class);

        Assert::assertNull($event, 'New locale should not be added');
        Assert::assertSame($reason, $this->reasonWhyCannotAddLocale);
    }

    /**
     * @Then new locale should be added
     */
    public function newLocaleShouldBeAdded(): void
    {
        $event = $this->websiteSpy->findEvent(LocaleAdded::class);

        Assert::assertInstanceOf(LocaleAdded::class, $event, 'New locale should be added');
    }

    /**
     * @When I deactivate this website
     */
    public function iDeactivateThisWebsite(): void
    {
        $this->website->deactivate();
    }

    /**
     * @Then website's activity should not be turned off
     */
    public function websitesActivityShouldNotBeTurnedOff(): void
    {
        $event = $this->websiteSpy->findEvent(WebsiteActivityChanged::class);

        Assert::assertNull($event, 'Activity should not be changed');
    }

    /**
     * @Then website's activity should be turned off
     */
    public function websitesActivityShouldBeTurnedOff(): void
    {
        $event = $this->websiteSpy->findEvent(WebsiteActivityChanged::class);

        Assert::assertInstanceOf(WebsiteActivityChanged::class, $event, 'Activity should be changed');
        Assert::assertFalse($event->active);
    }

    /**
     * @When I turn this website's activity on
     */
    public function iTurnThisWebsitesActivityOn(): void
    {
        $this->website->activate();
    }

    /**
     * @Then website's activity should not be turned on
     */
    public function websitesActivityShouldNotBeTurnedOn(): void
    {
        $event = $this->websiteSpy->findEvent(WebsiteActivityChanged::class);

        Assert::assertNull($event, 'Website activity should not be turned on');
    }

    /**
     * @Then website's activity should be turned on
     */
    public function websitesActivityShouldBeTurnedOn(): void
    {
        $event = $this->websiteSpy->findEvent(WebsiteActivityChanged::class);

        Assert::assertInstanceOf(WebsiteActivityChanged::class, $event, 'Website activity should be turned on');
        Assert::assertTrue($event->active);
    }

    /**
     * @Then website should not be updated
     */
    public function websiteShouldNotBeUpdated(): void
    {
        $event = $this->websiteSpy->findEvent(WebsiteUpdated::class);

        Assert::assertNull($event, 'Website should not be updated');
    }

    /**
     * @Then website should be updated
     */
    public function websiteShouldBeUpdated(): void
    {
        $event = $this->websiteSpy->findEvent(WebsiteUpdated::class);

        Assert::assertInstanceOf(WebsiteUpdated::class, $event, 'Website should be updated');
    }

    /**
     * @When I delete this website
     */
    public function iDeleteThisWebsite(): void
    {
        try {
            $this->website->delete(
                new CanDeleteWebsite(
                    $this->websitesCounterQuery,
                    $this->currentWebsitePrivider
                )
            );
        } catch (CannotDeleteWebsiteException $e) {
            $this->reasonWhyCannotDeleteWebsite = $e->reason;
        }
    }

    /**
     * @Then website should not be deleted, because :reason
     */
    public function websiteShouldNotBeDeletedBecause(string $reason): void
    {
        $event = $this->websiteSpy->findEvent(WebsiteDeleted::class);

        Assert::assertNull($event, 'Website should not be deleted');
        Assert::assertSame($reason, $this->reasonWhyCannotDeleteWebsite);
    }

    /**
     * @Given there is at least one other inactive website in system
     */
    public function thereIsAtLeastOneOtherInactiveWebsiteInSystem(): void
    {
        $this->websitesCounterQuery->makeReturnValue(0);
    }

    /**
     * @Given there is at least one other active website in system
     */
    public function thereIsAtLeastOneOtherActiveWebsiteInSystem(): void
    {
        $this->websitesCounterQuery->makeReturnValue(1);
    }

    /**
     * @Then website should be deleted
     */
    public function websiteShouldBeDeleted(): void
    {
        $event = $this->websiteSpy->findEvent(WebsiteDeleted::class);

        Assert::assertInstanceOf(WebsiteDeleted::class, $event, 'Website should be deleted');
    }

    /**
     * @Given I am on the website :website, and locale :code right now
     */
    public function iAmOnTheWebsiteRightNow(string $website, string $code): void
    {
        $this->currentWebsitePrivider->setCurrentWebsite($website, $code);
    }

    /**
     * @When I delete locale :code
     */
    public function iDeleteLocale(string $code): void
    {
        try {
            $this->website->deleteLocale(new CanDeleteLocale($this->currentWebsitePrivider), $code);
        } catch (CannotDeleteLocaleException $e) {
            $this->reasonWhyCannotDeleteLocale = $e->reason;
        }
    }

    /**
     * @Then locale should be deleted
     */
    public function localeShouldBeDeleted(): void
    {
        $event = $this->websiteSpy->findEvent(LocaleDeleted::class);

        Assert::assertInstanceOf(LocaleDeleted::class, $event, 'Locale should be deleted');
    }

    /**
     * @Then locale should not be deleted, because :reason
     */
    public function localeShouldNotBeDeletedBecause(string $reason): void
    {
        $event = $this->websiteSpy->findEvent(LocaleDeleted::class);

        Assert::assertNull($event, 'Locale should not be deleted');
        Assert::assertSame($reason, $this->reasonWhyCannotDeleteLocale);
    }

    /**
     * @When I turn this website's locale :code activity on
     */
    public function iTurnThisWebsitesLocaleActivityOn(string $code): void
    {
        $this->website->activateLocale($code);
    }

    /**
     * @When I turn this website's locale :code activity off
     */
    public function iTurnThisWebsitesLocaleActivityOff(string $code): void
    {
        $this->website->deactivateLocale($code);
    }

    /**
     * @Then website's locale :code activity should be turned on
     */
    public function websitesLocaleActivityShouldBeTurnedOn(string $code): void
    {
        $event = $this->websiteSpy->findEvent(LocaleActivityChanged::class);

        Assert::assertInstanceOf(LocaleActivityChanged::class, $event, 'Locale activity should be changed');
        Assert::assertSame($code, $event->code);
        Assert::assertTrue($event->active);
    }

    /**
     * @Then website's locale :code activity should be turned off
     */
    public function websitesLocaleActivityShouldBeTurnedOff(string $code): void
    {
        $event = $this->websiteSpy->findEvent(LocaleActivityChanged::class);

        Assert::assertInstanceOf(LocaleActivityChanged::class, $event, 'Locale activity should be changed');
        Assert::assertSame($code, $event->code);
        Assert::assertFalse($event->active);
    }

    /**
     * @Then website's locale :code activity should not be turned on
     */
    public function websitesLocaleActivityShouldNotBeTurnedOn(string $code): void
    {
        $event = $this->websiteSpy->findEvent(LocaleActivityChanged::class);

        Assert::assertNull($event, 'Locale activity should not be changed');
    }

    /**
     * @Then website's locale :code activity should not be turned off
     */
    public function websitesLocaleActivityShouldNotBeTurnedOff(string $code): void
    {
        $event = $this->websiteSpy->findEvent(LocaleActivityChanged::class);

        Assert::assertNull($event, 'Locale activity should not be changed');
    }

    private function id(): string
    {
        return (string) Uuid::v4();
    }
}
