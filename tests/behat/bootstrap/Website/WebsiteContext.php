<?php

declare(strict_types=1);

namespace Tulia\Cms\Tests\Behat\Website;

use Behat\Behat\Context\Context;
use Symfony\Component\Uid\Uuid;
use Tulia\Cms\Tests\Behat\AggregateRootSpy;
use Tulia\Cms\Tests\Behat\Assert;
use Tulia\Cms\Website\Domain\WriteModel\Event\WebsiteCreated;
use Tulia\Cms\Website\Domain\WriteModel\Model\Website;

/**
 * @author Adam Banaszkiewicz
 */
final class WebsiteContext implements Context
{
    private Website $website;
    private AggregateRootSpy $websiteSpy;

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

    private function id(): string
    {
        return (string) Uuid::v4();
    }
}
