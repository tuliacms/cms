<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\ObjectImporter\Decorator;

use Tulia\Component\Importer\Exception\MissingParameterException;
use Tulia\Component\Importer\ObjectImporter\ObjectImporterInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface ObjectImporterDecoratorInterface
{
    /**
     * @throws MissingParameterException
     */
    public function decorate(ObjectImporterInterface $importer, array $parameters): ObjectImporterInterface;
}
