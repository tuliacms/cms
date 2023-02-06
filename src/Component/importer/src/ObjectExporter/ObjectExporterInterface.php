<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\ObjectExporter;

use Tulia\Component\Importer\ObjectExporter\ObjectsCollection\ObjectsCollection;
use Tulia\Component\Importer\Structure\ObjectData;

/**
 * @author Adam Banaszkiewicz
 */
interface ObjectExporterInterface
{
    public function collectObjects(ObjectsCollection $collection): void;

    public function export(ObjectData $objectData): void;
}
