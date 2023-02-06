<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\ObjectExporter\Decorator;

use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteInterface;
use Tulia\Component\Importer\ObjectExporter\ObjectExporterInterface;
use Tulia\Component\Importer\ObjectExporter\Traits\WebsiteAwareTrait;

/**
 * @author Adam Banaszkiewicz
 */
final class WebsiteDecorator implements ObjectExporterDecoratorInterface
{
    public function __construct(
        private readonly WebsiteInterface $website,
    ) {
    }

    public function decorate(ObjectExporterInterface $exporter, array $parameters): ObjectExporterInterface
    {
        if (\in_array(WebsiteAwareTrait::class, class_uses($exporter), true)) {
            $exporter->setWebsite($parameters['website'] ?? $this->website);
        }

        return $exporter;
    }
}
