<?php

declare(strict_types=1);

namespace Tulia\Component\Importer;

use Tulia\Component\Importer\FileIO\FileIORegistryInterface;
use Tulia\Component\Importer\ObjectImporter\ObjectImporterRegistry;
use Tulia\Component\Importer\Parameters\ParametersCompiler;
use Tulia\Component\Importer\Parameters\ParametersProviderInterface;
use Tulia\Component\Importer\Schema\ObjectsIdGenerator;
use Tulia\Component\Importer\Structure\Dependencies;
use Tulia\Component\Importer\Structure\ObjectData;
use Tulia\Component\Importer\Structure\StructureSorter;
use Tulia\Component\Importer\Validation\SchemaValidatorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Importer implements ImporterInterface
{
    public function __construct(
        private readonly FileIORegistryInterface $fileIORegistry,
        private readonly SchemaValidatorInterface $schemaValidator,
        private readonly ObjectImporterRegistry $importerRegistry,
        private readonly ParametersProviderInterface $parametersProvider,
    ) {
    }

    public function importFromFile(string $filepath, ?string $realFilename = null, array $parameters = []): void
    {
        $data = $this->fileIORegistry
            ->getSupported($realFilename ?? $filepath)
            ->read($filepath);

        $this->import($data, dirname($filepath), $parameters);
    }

    private function import(array $data, string $importRootPath, array $parameters = []): void
    {
        $data['objects'] = (new ParametersCompiler($this->parametersProvider))
            ->compileParametersInValues($data['objects']);

        $objects = $this->schemaValidator->validate($data['objects'], $importRootPath);
        $objects = (new ObjectsIdGenerator())->generateMissingIds($objects);
        $objects = (new Dependencies())->fetchDependencies($objects);
        $objects = (new StructureSorter())->sortObjects($objects);

        $this->importObjectCollection($objects, $parameters);
    }

    /**
     * @param ObjectData[] $objects
     */
    private function importObjectCollection(array $objects, array $parameters): void
    {
        foreach ($objects as $object) {
            if ($object->getDefinition()->getImporter() === null) {
                continue;
            }

            $fakeObjectId = $object['@id'];
            $realObjectId = $this->importerRegistry
                ->getImporter($object->getDefinition()->getImporter(), $parameters)
                ->import($object);

            $this->updateObjectsIdToRealId($objects, $fakeObjectId, $realObjectId);
            $object['@id'] = $realObjectId;
        }
    }

    /**
     * @param ObjectData[] $objects
     */
    private function updateObjectsIdToRealId(
        array $objects,
        string $fakeObjectId,
        ?string $realObjectId
    ): void {
        if (!$realObjectId) {
            return;
        }

        foreach ($objects as $object) {
            foreach ($object['@@dependencies'] as $dependency) {
                if ($dependency['@id'] === $fakeObjectId) {
                    $dependency['@handle']->replaceWith($realObjectId);
                }
            }
        }
    }
}
