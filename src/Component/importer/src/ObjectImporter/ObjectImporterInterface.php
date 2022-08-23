<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\ObjectImporter;

use Tulia\Component\Importer\Structure\ObjectData;

/**
 * @author Adam Banaszkiewicz
 */
interface ObjectImporterInterface
{
    /**
     * Imports object, and returns it's ID.
     */
    public function import(ObjectData $objectData): ?string;

    public function getAuthorId(): string;
}
