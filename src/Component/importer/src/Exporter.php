<?php

declare(strict_types=1);

namespace Tulia\Component\Importer;

use Tulia\Component\Importer\FileIO\FileIORegistryInterface;
use Tulia\Component\Importer\ObjectExporter\ObjectExporterRegistry;
use Tulia\Component\Importer\ObjectExporter\ObjectsCollection\ObjectsCollection;
use Tulia\Component\Importer\Schema\ExportedObjectsIdProcessor;
use Tulia\Component\Importer\Structure\ObjectDataFactory;

/**
 * @author Adam Banaszkiewicz
 */
final class Exporter implements ExporterInterface
{
    public function __construct(
        private readonly FileIORegistryInterface $fileIORegistry,
        private readonly ObjectExporterRegistry $exporterRegistry,
        private readonly ObjectDataFactory $objectDataFactory,
        private readonly string $projectDir,
    ) {
    }

    public function collectObjects(array $parameters = []): ObjectsCollection
    {
        $objects = new ObjectsCollection();

        foreach ($this->exporterRegistry->all($parameters) as $exporter) {
            $exporter->collectObjects($objects);
        }

        return $objects;
    }

    public function export(array $objectIdList, array $parameters = []): string
    {
        $objects = [];

        foreach ($objectIdList as $val) {
            [$type, $objectId] = explode(':', $val);

            $object = $this->objectDataFactory->createEmpty($type, $objectId);

            $this->exporterRegistry
                ->getExporter($object->getDefinition()->getExporter(), $parameters)
                ->export($object);

            $objects[] = $object;
        }

        $objects = (new ExportedObjectsIdProcessor())->processObjects($objects);

        $filepath = $this->projectDir.'/var/export/export-'.date('Y-m-d_H-i-s').'.json';

        $this->fileIORegistry->getSupported($filepath)
            ->write($filepath, ['objects' => array_map(static fn($v) => $v->toArray(), $objects)]);

        return $filepath;
    }
}
