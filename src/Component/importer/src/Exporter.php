<?php

declare(strict_types=1);

namespace Tulia\Component\Importer;

use Tulia\Component\Importer\FileIO\FileIORegistryInterface;
use Tulia\Component\Importer\ObjectExporter\ObjectExporterRegistry;
use Tulia\Component\Importer\ObjectExporter\ObjectsCollection\ObjectsCollection;
use Tulia\Component\Importer\Schema\ExportedObjectsIdProcessor;
use Tulia\Component\Importer\Structure\ObjectDataFactory;
use Tulia\Component\Importer\ZipArchive\ZipArchive;

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
        $basedir = $this->projectDir.'/var/export';
        $directory = $basedir.'/export-'.date('Y-m-d_H-i-s');
        $collection = $directory.'/collection.json';
        $archive = $basedir.'/collection-export-'.date('Y-m-d_H-i-s').'.zip';

        $exporter = $this->fileIORegistry->getSupported($collection);
        $exporter->ensureDirectoryExists($directory);

        // @todo Eee, no, this should be done in other way... Service cannot dependends on values from setters!
        $this->objectDataFactory->setWorkdir($directory);

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

        $exporter->write($collection, ['objects' => array_map(static fn($v) => $v->toArray(), $objects)]);

        $zip = ZipArchive::create($archive);
        $zip->addFilesFrom($directory);
        $zip->close();

        $exporter->ensureDirectoryDoesNotExists($directory);

        return $archive;
    }
}
