<?php

declare(strict_types=1);

namespace Tulia\Cms\Tests\Behat\Website\TestDoubles;

use Tulia\Cms\Website\Domain\WriteModel\Query\CurrentWebsiteProviderInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class StubCurrentWebsiteProvider implements CurrentWebsiteProviderInterface
{
    private string $currentWebsite = '';

    public function setCurrentWebsite(string $currentWebsite): void
    {
        $this->currentWebsite = $currentWebsite;
    }

    public function getId(): string
    {
        return $this->currentWebsite;
    }
}
