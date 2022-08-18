<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\ObjectImporter\Traits;

use Tulia\Component\Routing\Website\WebsiteInterface;

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
