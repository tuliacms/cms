<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\Schema;

use Tulia\Component\Importer\Structure\ObjectData;

/**
 * @author Adam Banaszkiewicz
 */
final class ExportedObjectsIdProcessor
{
    private readonly ObjectsIdGenerator $idGenerator;
    private array $idMap = [];

    public function __construct()
    {
        $this->idGenerator = new ObjectsIdGenerator();
    }

    /**
     * @param ObjectData[] $objects
     * @return ObjectData[]
     */
    public function processObjects(array $objects): array
    {
        $objects = $this->idGenerator->generateMissingIds($objects);
        $objects = $this->generateObjectsIdMap($objects);

        foreach ($this->idMap as $oldId => $newId) {
            foreach ($objects as $object) {
                $this->replaceIdInContents($object, $newId['type'], $oldId, $newId['id']);
            }
        }

        return $objects;
    }

    /**
     * @param ObjectData[] $objects
     * @return ObjectData[]
     */
    private function generateObjectsIdMap(array $objects): array
    {
        $newObjects = [];

        foreach ($objects as $object) {
            $newId = $this->idGenerator->generateId();

            $this->idMap[$object->getObjectId()] = [
                'id' => $newId,
                'type' => $object->getObjectType(),
            ];
            $newOne = $object->withNewId($newId);

            foreach ($object->getDefinition()->getFields() as $field) {
                if ($field->isCollection()) {
                    $object[$field->getName()] = $this->generateObjectsIdMap($object[$field->getName()]);
                }
            }

            $newObjects[] = $newOne;
        }

        return $newObjects;
    }

    private function replaceIdInContents(ObjectData $object, string $type, string $oldId, string $newId): void
    {
        foreach ($object->getDefinition()->getFields() as $field) {
            $code = $field->getName();

            if (!isset($object[$code])) {
                continue;
            }

            if ($field->isCollection()) {
                foreach ($object[$code] as $inner) {
                    $this->replaceIdInContents($inner, $type, $oldId, $newId);
                }
            } else {
                $newIdDefinition = "[[@{$type}:{$newId}]]";

                if (is_array($object[$code])) {
                    $values = $object[$code];

                    foreach ($values as $key => $val) {
                        if (is_string($object[$code])) {
                            $values[$key] = str_replace($oldId, $newIdDefinition, $val);
                        }
                    }

                    $object[$code] = $values;
                } else {
                    if (is_string($object[$code])) {
                        $object[$code] = str_replace($oldId, $newIdDefinition, $object[$code]);
                    }
                }
            }
        }
    }
}
