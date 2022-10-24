<?php

declare(strict_types=1);

namespace Tulia\Cms\Tests\Behat\Website;

use Tulia\Cms\Tests\Behat\AggregateRootSpy;
use Tulia\Cms\Tests\Helper\ObjectMother\WebsiteMother;
use Tulia\Cms\Website\Domain\WriteModel\Model\Website;

/**
 * @property Website $website
 * @property AggregateRootSpy $websiteSpy
 * @author Adam Banaszkiewicz
 */
trait WebsiteBuildableTrait
{
    private WebsiteMother $websiteMother;

    public function __get(string $name)
    {
        if ($name !== 'website') {
            throw new \LogicException(sprintf('You can get only Website through magic getter, "%s" called.', $name));
        }

        if (false === property_exists($this, 'website')) {
            $this->website = $this->websiteMother->build();
            $this->websiteSpy = new AggregateRootSpy($this->website);

        }

        return $this->website;
    }

    /**
     * @Given there is a website :name, with default locale :code
     */
    public function thereIsAWebsiteWithDefaultLocale(string $name, string $code): void
    {
        $this->websiteMother = WebsiteMother::aWebsite($name)->withDefaultLocale($code);
    }

    /**
     * @Given which have locale :code
     */
    public function whichHaveLocale(string $code): void
    {
        $this->websiteMother->withLocale($code);
    }

    /**
     * @Given which have inactive locale :code
     */
    public function whichHaveInactiveLocale($code): void
    {
        $this->websiteMother->withInactiveLocale($code);
    }

    /**
     * @Given which is inactive
     */
    public function whichIsInactive()
    {
        $this->websiteMother->isInactive();
    }
}
