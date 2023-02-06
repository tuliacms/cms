<?php

declare(strict_types=1);

namespace Tulia\Component\Importer;

use Tulia\Component\Importer\ObjectExporter\ObjectsCollection\ObjectsCollection;

/**
 * @author Adam Banaszkiewicz
 */
interface ExporterInterface
{
    public function collectObjects(array $parameters = []): ObjectsCollection;

    public function export(array $objectIdList, array $parameters = []): string;
}
