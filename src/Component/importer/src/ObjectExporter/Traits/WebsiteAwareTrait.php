<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\ObjectExporter\Traits;

use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
trait WebsiteAwareTrait
{
    private WebsiteInterface $website;

    public function setWebsite(WebsiteInterface $website): void
    {
        $this->website = $website;
    }

    public function getWebsite(): WebsiteInterface
    {
        return $this->website;
    }
}
