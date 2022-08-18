<?php

declare(strict_types=1);

namespace Tulia\Component\Importer;

use Tulia\Component\Importer\FileReader\FileReaderRegistryInterface;
use Tulia\Component\Importer\ObjectImporter\ObjectImporterRegistry;
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
        private FileReaderRegistryInterface $fileReaderRegistry,
        private SchemaValidatorInterface $schemaValidator,
        private ObjectImporterRegistry $importerRegistry,
    ) {
    }

    public function importFromFile(string $filepath, ?string $realFilename = null): void
    {
        $data = $this->fileReaderRegistry
            ->getSupportingReader($realFilename ?? $filepath)
            ->read($filepath);

        $this->import($data);
    }

    public function import(array $data): void
    {
        $objects = $this->schemaValidator->validate($data['objects']);
        $objects = (new ObjectsIdGenerator())->generateMissingIds($objects);
        $objects = (new Dependencies())->fetchDependencies($objects);
        $objects = (new StructureSorter())->sortObjects($objects);

        $this->importObjectCollection($objects);
    }

    /**
     * @param ObjectData[] $objects
     */
    private function importObjectCollection(array $objects): void
    {
        foreach ($objects as $object) {
            if ($object->getDefinition()->getImporter() === null) {
                continue;
            }

            $fakeObjectId = $object['@id'];
            $realObjectId = $this->importerRegistry
                ->getImporter($object->getDefinition()->getImporter())
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
