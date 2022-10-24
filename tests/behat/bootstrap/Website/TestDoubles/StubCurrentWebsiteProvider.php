<?php

declare(strict_types=1);

namespace Tulia\Cms\Tests\Behat\Website\TestDoubles;

use Tulia\Cms\Website\Domain\WriteModel\Query\CurrentWebsiteProviderInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class StubCurrentWebsiteProvider implements CurrentWebsiteProviderInterface
{
    private string $website = '';
    private string $locale = '';

    public function setCurrentWebsite(string $website, string $locale): void
    {
        $this->website = $website;
        $this->locale = $locale;
    }

    public function getId(): string
    {
        return $this->website;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }
}
