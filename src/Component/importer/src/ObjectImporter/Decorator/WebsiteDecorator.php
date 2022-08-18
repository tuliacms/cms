<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\ObjectImporter\Decorator;

use Tulia\Component\Importer\ObjectImporter\ObjectImporterInterface;
use Tulia\Component\Importer\ObjectImporter\WebsiteAwareObjectImporterInterface;
use Tulia\Component\Routing\Website\WebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class WebsiteDecorator implements ObjectImporterDecoratorInterface
{
    public function __construct(
        private readonly WebsiteInterface $website
    ) {
    }

    public function decorate(ObjectImporterInterface $importer): ObjectImporterInterface
    {
        if ($importer instanceof WebsiteAwareObjectImporterInterface) {
            $importer->setWebsite($this->website);
        }

        return $importer;
    }
}
