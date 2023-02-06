<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\ObjectImporter\Decorator;

use Tulia\Component\Importer\Exception\MissingParameterException;
use Tulia\Component\Importer\ObjectImporter\ObjectImporterInterface;
use Tulia\Component\Importer\ObjectImporter\Traits\AuthorAwareTrait;

/**
 * @author Adam Banaszkiewicz
 */
final class AuthorDecorator implements ObjectExporterDecoratorInterface
{
    public function decorate(ObjectImporterInterface $importer, array $parameters): ObjectImporterInterface
    {
        if (\in_array(AuthorAwareTrait::class, class_uses($importer), true)) {
            if (!isset($parameters['author_id'])) {
                throw MissingParameterException::fromName('author_id');
            }

            $importer->setAuthorId($parameters['author_id']);
        }

        return $importer;
    }
}
