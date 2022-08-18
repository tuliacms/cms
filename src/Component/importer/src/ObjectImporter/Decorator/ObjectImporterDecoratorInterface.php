<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\ObjectImporter\Decorator;

use Tulia\Component\Importer\ObjectImporter\ObjectImporterInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface ObjectImporterDecoratorInterface
{
    public function decorate(ObjectImporterInterface $importer): ObjectImporterInterface;
}
