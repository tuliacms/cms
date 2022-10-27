<?php

declare(strict_types=1);

namespace Tulia\Cms\Tests\Behat\Website;

use Behat\Behat\Context\Context;
use Symfony\Component\Uid\Uuid;
use Tulia\Cms\Tests\Behat\AggregateRootSpy;
use Tulia\Cms\Tests\Behat\Assert;
use Tulia\Cms\Tests\Behat\Website\TestDoubles\StubCurrentWebsiteProvider;
use Tulia\Cms\Tests\Behat\Website\TestDoubles\StubWebsitesCounterQuery;
use Tulia\Cms\Website\Domain\WriteModel\Event\LocaleDisabled;
use Tulia\Cms\Website\Domain\WriteModel\Event\LocaleEnabled;
use Tulia\Cms\Website\Domain\WriteModel\Event\LocaleAdded;
use Tulia\Cms\Website\Domain\WriteModel\Event\LocaleDeleted;
use Tulia\Cms\Website\Domain\WriteModel\Event\WebsiteDisabled;
use Tulia\Cms\Website\Domain\WriteModel\Event\WebsiteEnabled;
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
     * @When I disable this website
     */
    public function iDisableThisWebsite(): void
    {
        $this->website->disable();
    }

    /**
     * @Then website should not be disabled
     */
    public function websiteShouldNotBeDisabled(): void
    {
        $event = $this->websiteSpy->findEvent(WebsiteDisabled::class);

        Assert::assertNull($event, 'Website should not be disabled');
    }

    /**
     * @Then website should be disabled
     */
    public function websiteShouldBeDisabled(): void
    {
        $event = $this->websiteSpy->findEvent(WebsiteDisabled::class);

        Assert::assertInstanceOf(WebsiteDisabled::class, $event, 'Website should be disabled');
    }

    /**
     * @When I enable this website
     */
    public function iEnableThisWebsite(): void
    {
        $this->website->enable();
    }

    /**
     * @Then website should not be enabled
     */
    public function websiteShouldNotBeEeabled(): void
    {
        $event = $this->websiteSpy->findEvent(WebsiteEnabled::class);

        Assert::assertNull($event, 'Website should not be enabled');
    }

    /**
     * @Then website should be enabled
     */
    public function websiteShouldBeEnabled(): void
    {
        $event = $this->websiteSpy->findEvent(WebsiteEnabled::class);

        Assert::assertInstanceOf(WebsiteEnabled::class, $event, 'Website should be enabled');
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
     * @When I enable :code of this website
     */
    public function iEnableLocale(string $code): void
    {
        $this->website->enableLocale($code);
    }

    /**
     * @When I disable locale :code of this website
     */
    public function iDisableLocale(string $code): void
    {
        $this->website->disableLocale($code);
    }

    /**
     * @Then website's locale :code should be enabled
     */
    public function websitesLocaleShouldBeEnabled(string $code): void
    {
        $event = $this->websiteSpy->findEvent(LocaleEnabled::class);

        Assert::assertInstanceOf(LocaleEnabled::class, $event, 'Locale should be enabled');
        Assert::assertSame($code, $event->code);
    }

    /**
     * @Then website's locale :code should be disabled
     */
    public function websitesLocaleShouldBeDisabled(string $code): void
    {
        $event = $this->websiteSpy->findEvent(LocaleDisabled::class);

        Assert::assertInstanceOf(LocaleDisabled::class, $event, 'Locale should be disabled');
        Assert::assertSame($code, $event->code);
    }

    /**
     * @Then website's locale :code should not be enabled
     */
    public function websitesLocaleShouldNotBeEnabled(string $code): void
    {
        $event = $this->websiteSpy->findEvent(LocaleEnabled::class);

        Assert::assertNull($event, 'Locale should not be enabled');
    }

    /**
     * @Then website's locale :code should not be disabled
     */
    public function websitesLocaleShouldNotBeDisabled(string $code): void
    {
        $event = $this->websiteSpy->findEvent(LocaleDisabled::class);

        Assert::assertNull($event, 'Locale should not be disabled');
    }

    private function id(): string
    {
        return (string) Uuid::v4();
    }
}
