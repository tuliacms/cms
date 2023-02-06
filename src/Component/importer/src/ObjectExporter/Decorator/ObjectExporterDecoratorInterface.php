<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\ObjectExporter\Decorator;

use Tulia\Component\Importer\Exception\MissingParameterException;
use Tulia\Component\Importer\ObjectExporter\ObjectExporterInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface ObjectExporterDecoratorInterface
{
    /**
     * @throws MissingParameterException
     */
    public function decorate(ObjectExporterInterface $exporter, array $parameters): ObjectExporterInterface;
}
