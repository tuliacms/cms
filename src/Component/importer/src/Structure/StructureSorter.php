<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\Structure;

use MJS\TopSort\Implementations\StringSort;

/**
 * @author Adam Banaszkiewicz
 */
final class StructureSorter
{
    /**
     * @param ObjectData[] $objects
     * @return ObjectData[]
     */
    public function sortObjects(array $objects): array
    {
        $flattenedObjects = $this->flattenObjects($objects);
        $dependencies = $this->collectAllDependencies($flattenedObjects);
        $dependencies = $this->sortDependencies($dependencies);

        $newObjectsSort = $this->sortFromRootUsingDependenciesOrder($flattenedObjects, $dependencies);

        return $newObjectsSort;
    }

    /**
     * @param ObjectData[] $objects
     * @return ObjectData[]
     */
    private function sortFromRootUsingDependenciesOrder(array $objects, array $dependencies): array
    {
        $toSort = array_filter($objects, static fn($o) => $o->getDefinition()->isRoot());

        $objectsSorted = $this->collectOrdered($objects, $toSort, $dependencies);

        foreach ($objectsSorted as $object) {
            foreach ($object->getDefinition()->getFields() as $field) {
                if ($field->isCollection()) {
                    $object[$field->getName()] = $this->sortCollectionUsingDependenciesOrder(
                        $objects,
                        $object[$field->getName()],
                        $dependencies
                    );
                }
            }
        }

        return $objectsSorted;
    }

    /**
     * @param ObjectData[] $allObjects
     * @param ObjectData[] $objectsToSort
     * @return ObjectData[]
     */
    private function sortCollectionUsingDependenciesOrder(array $allObjects, array $objectsToSort, array $dependencies): array
    {
        $objectsSorted = $this->collectOrdered($allObjects, $objectsToSort, $dependencies);

        foreach ($objectsSorted as $object) {
            foreach ($object->getDefinition()->getFields() as $field) {
                if ($field->isCollection()) {
                    $object[$field->getName()] = $this->sortCollectionUsingDependenciesOrder(
                        $allObjects,
                        $object[$field->getName()],
                        $dependencies
                    );
                }
            }
        }

        return $objectsSorted;
    }

    /**
     * @param ObjectData[] $allObjects
     * @param ObjectData[] $objectsToSort
     * @return ObjectData[]
     */
    private function collectOrdered(array $allObjects, array $objectsToSort, array $dependencies): array
    {
        $objectsSorted = [];

        foreach ($dependencies as $dependency) {
            [, $id] = explode(':', $dependency);

            foreach ($objectsToSort as $object) {
                if ($object->getObjectId() === $id) {
                    $objectsSorted[] = $this->getObject($allObjects, $id);
                }
            }
        }

        return $objectsSorted;
    }

    /**
     * @param ObjectData[] $objects
     */
    private function collectAllDependencies(array $objects): array
    {
        $dependencies = [];

        foreach ($objects as $object) {
            $dependencies[] = [
                'id' => $object->getObjectType().':'.$object->getObjectId(),
                'deps' => $this->findDependencies($object),
            ];
        }

        return $dependencies;
    }

    /**
     * @return ObjectData[]
     */
    private function findDependencies(ObjectData $object): array
    {
        $dependencies = [];

        foreach ($object['@@dependencies'] as $dependency) {
            $dependencies[] = $dependency['@type'].':'.$dependency['@id'];
        }

        return array_unique($dependencies);
    }

    private function sortDependencies(array $items): array
    {
        $sorter = new StringSort();

        foreach ($items as $item) {
            $sorter->add($item['id'], $item['deps']);
        }

        return $sorter->sort();
    }

    /**
     * @param ObjectData[] $objects
     */
    private function getObject(array $objects, string $id): ObjectData
    {
        foreach ($objects as $object) {
            if ($object->getObjectId() === $id) {
                return $object;
            }
        }

        throw new \RuntimeException(sprintf('Cannot find object %s.', $id));
    }

    /**
     * @param ObjectData[] $objects
     */
    private function flattenObjects(array $objects): array
    {
        $flattened = [];

        foreach ($objects as $object) {
            $flattened[] = [$object];

            foreach ($object->getDefinition()->getFields() as $field) {
                if ($field->isCollection()) {
                    $flattened[] = $this->flattenObjects($object[$field->getName()]);
                }
            }
        }

        if (empty($flattened)) {
            return [];
        }

        return array_merge(...$flattened);
    }
}
