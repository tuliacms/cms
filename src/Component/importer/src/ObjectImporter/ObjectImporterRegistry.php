<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\ObjectImporter;

use Tulia\Component\Importer\ObjectImporter\Decorator\ObjectImporterDecoratorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ObjectImporterRegistry
{
    /** @var ObjectImporterInterface[]  */
    private array $importers = [];
    /** @var ObjectImporterDecoratorInterface[]  */
    private array $decorators = [];

    public function addImporterDecorator(ObjectImporterDecoratorInterface $decorator): void
    {
        $this->decorators[get_class($decorator)] = $decorator;
    }

    public function addObjectImporter(ObjectImporterInterface $importer): void
    {
        $this->importers[get_class($importer)] = $importer;
    }

    public function getImporter(string $classname, array $parameters): ObjectImporterInterface
    {
        return $this->decorate($this->importers[$classname], $parameters);
    }

    private function decorate(ObjectImporterInterface $importer, array $parameters): ObjectImporterInterface
    {
        foreach ($this->decorators as $decorator) {
            $importer = $decorator->decorate($importer, $parameters);
        }

        return $importer;
    }
}
