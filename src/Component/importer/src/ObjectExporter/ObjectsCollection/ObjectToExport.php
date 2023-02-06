<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\ObjectExporter\ObjectsCollection;

/**
 * @author Adam Banaszkiewicz
 */
final class ObjectToExport
{
    public function __construct(
        public readonly string $type,
        public readonly string $id,
        public readonly string $name,
    ) {
    }
}
