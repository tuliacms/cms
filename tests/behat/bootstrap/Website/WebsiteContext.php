<?php

declare(strict_types=1);

namespace Tulia\Cms\Tests\Behat\Website;

use Behat\Behat\Context\Context;
use Symfony\Component\Uid\Uuid;
use Tulia\Cms\Tests\Behat\AggregateRootSpy;
use Tulia\Cms\Tests\Behat\Assert;
use Tulia\Cms\Website\Domain\WriteModel\Event\LocaleAdded;
use Tulia\Cms\Website\Domain\WriteModel\Event\WebsiteCreated;
use Tulia\Cms\Website\Domain\WriteModel\Exception\CannotAddLocaleException;
use Tulia\Cms\Website\Domain\WriteModel\Model\Website;
use Tulia\Cms\Website\Domain\WriteModel\Rules\CannAddLocale\CanAddLocale;

/**
 * @author Adam Banaszkiewicz
 */
final class WebsiteContext implements Context
{
    use WebsiteBuildableTrait;

    private ?AggregateRootSpy $websiteSpy = null;
    private ?string $reasonWhyCannotAddLocale = null;

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

    private function id(): string
    {
        return (string) Uuid::v4();
    }
}
