<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\ObjectImporter;

use Tulia\Component\Routing\Website\WebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface WebsiteAwareObjectImporterInterface
{
    public function setWebsite(WebsiteInterface $website): void;
    public function getWebsite(): WebsiteInterface;
}
