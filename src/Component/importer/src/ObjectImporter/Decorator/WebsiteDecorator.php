<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\ObjectImporter\Decorator;

use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteInterface;
use Tulia\Component\Importer\ObjectImporter\ObjectImporterInterface;
use Tulia\Component\Importer\ObjectImporter\Traits\WebsiteAwareTrait;

/**
 * @author Adam Banaszkiewicz
 */
final class WebsiteDecorator implements ObjectExporterDecoratorInterface
{
    public function __construct(
        private readonly WebsiteInterface $website,
    ) {
    }

    public function decorate(ObjectImporterInterface $importer, array $parameters): ObjectImporterInterface
    {
        if (\in_array(WebsiteAwareTrait::class, class_uses($importer), true)) {
            $importer->setWebsite($parameters['website'] ?? $this->website);
        }

        return $importer;
    }
}
