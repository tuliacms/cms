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
    private array $existingIdList = [];

    /**
     * @param ObjectData[] $objects
     * @return ObjectData[]
     */
    public function generateMissingIds(array $objects): array
    {
        $this->collectExistingIds($objects);

        return $this->generateMissingIdsExceptExistingOnes($objects);
    }

    public function generateId(): string
    {
        do {
            $id = (string) Uuid::v4();
        } while (in_array($id, $this->existingIdList));

        $this->existingIdList[] = $id;

        return $id;
    }

    /**
     * @param ObjectData[] $objects
     * @return string[]
     */
    private function collectExistingIds(array $objects): void
    {
        $ids = [];

        foreach ($objects as $object) {
            $ids[] = $this->collectObjectIds($object);
        }

        $this->existingIdList += array_filter(array_merge(...$ids));
    }

    /**
     * @return string[]
     */
    private function collectObjectIds(ObjectData $object): array
    {
        $ids = [];

        foreach ($object->getDefinition()->getFields() as $field) {
            if ($field->isCollection()) {
                $this->collectExistingIds($object[$field->getName()]);
            }
        }

        if (isset($object['@id'])) {
            $ids[] = [$object['@id']];
        }

        return array_filter(array_merge(...$ids));
    }

    /**
     * @param ObjectData[] $objects
     * @return ObjectData[]
     */
    private function generateMissingIdsExceptExistingOnes(array $objects): array
    {
        $newObjects = [];

        foreach ($objects as $object) {
            if (!isset($object['@id'])) {
                $object['@id'] = $this->generateId();
            }

            $newObjects[$object['@id']] = $object;

            foreach ($object->getDefinition()->getFields() as $field) {
                if ($field->isCollection()) {
                    $newObjects[$object['@id']][$field->getName()] = $this->generateMissingIdsExceptExistingOnes($object[$field->getName()]);
                }
            }
        }

        return array_values($newObjects);
    }
}
