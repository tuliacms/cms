<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Infrastructure\Framework\Routing\Website;

use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteInterface;
use Tulia\Cms\Website\Domain\WriteModel\Query\CurrentWebsiteProviderInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class FrameworkCurrentWebsiteProvider implements CurrentWebsiteProviderInterface
{
    public function __construct(
        private readonly WebsiteInterface $website,
    ) {
    }

    public function getId(): string
    {
        return $this->website->getId();
    }

    public function getLocale(): string
    {
        return $this->website->getLocale()->getCode();
    }
}
