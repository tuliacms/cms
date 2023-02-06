<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\ObjectExporter;

use Tulia\Component\Importer\ObjectExporter\Decorator\ObjectExporterDecoratorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ObjectExporterRegistry
{
    /** @var ObjectExporterInterface[]  */
    private array $exporters = [];
    /** @var ObjectExporterDecoratorInterface[]  */
    private array $decorators = [];

    public function addExporterDecorator(ObjectExporterDecoratorInterface $decorator): void
    {
        $this->decorators[get_class($decorator)] = $decorator;
    }

    public function addObjectExporter(ObjectExporterInterface $importer): void
    {
        $this->exporters[get_class($importer)] = $importer;
    }

    public function getExporter(string $classname, array $parameters): ObjectExporterInterface
    {
        return $this->decorate($this->exporters[$classname], $parameters);
    }

    /**
     * @return ObjectExporterInterface[]
     */
    public function all(array $parameters): iterable
    {
        foreach ($this->exporters as $classname => $exporter) {
            yield $this->getExporter($classname, $parameters);
        }
    }

    private function decorate(ObjectExporterInterface $importer, array $parameters): ObjectExporterInterface
    {
        foreach ($this->decorators as $decorator) {
            $importer = $decorator->decorate($importer, $parameters);
        }

        return $importer;
    }
}
