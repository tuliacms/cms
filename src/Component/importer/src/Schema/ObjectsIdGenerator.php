<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\Schema;

use Symfony\Component\Uid\Uuid;
use Tulia\Component\Importer\Structure\ObjectData;

/**
 * @author Adam Banaszkiewicz
 */
final class ObjectsIdGenerator
{
    /**
     * @param ObjectData[] $objects
     * @return ObjectData[]
     */
    public function generateMissingIds(array $objects): array
    {
        $existingIds = $this->collectExistingIds($objects);

        return $this->generateMissingIdsExceptExistingOnes($objects, $existingIds);
    }

    /**
     * @param ObjectData[] $objects
     * @return string[]
     */
    private function collectExistingIds(array $objects): array
    {
        $ids = [];

        foreach ($objects as $object) {
            $ids[] = $this->collectObjectIds($object);
        }

        return array_filter(array_merge(...$ids));
    }

    /**
     * @return string[]
     */
    private function collectObjectIds(ObjectData $object): array
    {
        $ids = [];

        foreach ($object->getDefinition()->getFields() as $field) {
            if ($field->isCollection()) {
                $ids[] = $this->collectExistingIds($object[$field->getName()]);
            }
        }

        if (isset($object['@id'])) {
            $ids[] = [$object['@id']];
        }

        return array_filter(array_merge(...$ids));
    }

    /**
     * @param ObjectData[] $objects
     * @param string[] $existingIds
     * @return ObjectData[]
     */
    private function generateMissingIdsExceptExistingOnes(array $objects, array &$existingIds): array
    {
        $newObjects = [];

        foreach ($objects as $object) {
            if (!isset($object['@id'])) {
                $object['@id'] = $this->generateId($existingIds);
            }

            $newObjects[$object['@id']] = $object;

            foreach ($object->getDefinition()->getFields() as $field) {
                if ($field->isCollection()) {
                    $newObjects[$object['@id']][$field->getName()] = $this->generateMissingIdsExceptExistingOnes($object[$field->getName()], $existingIds);
                }
            }
        }

        return $newObjects;
    }

    private function generateId(array &$existingIds): string
    {
        do {
            $id = (string)Uuid::v4();
        } while (in_array($id, $existingIds));

        return $id;
    }
}
