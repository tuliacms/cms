<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\Structure;

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
        $dependencies = [];

        foreach ($objects as $key => $object) {
            $dependencies[$key]['id'] = $key;
            $dependencies[$key]['deps'] = $this->findDependencies($object);
        }

        $dependencies = $this->sortDependencies($dependencies);

        $newObjectsSort = [];

        foreach ($dependencies as $dependency) {
            $newObjectsSort[] = $this->findObject($objects, $dependency['id']);
        }

        return $newObjectsSort;
    }

    /**
     * @return ObjectData[]
     */
    private function findDependencies(ObjectData $object): array
    {
        $dependencies = [];

        foreach ($object['@@dependencies'] as $dependency) {
            $dependencies[] = $dependency['@id'];
        }

        return array_unique($dependencies);
    }

    private function sortDependencies(array $items): array
    {
        $res = [];
        $doneList = [];

        // while not all items are resolved:
        while(count($items) > count($res)) {
            $doneSomething = false;

            foreach($items as $itemIndex => $item) {
                if(isset($doneList[$item['id']])) {
                    // item already in resultset
                    continue;
                }

                $resolved = true;

                foreach($item['deps'] as $dep) {
                    if(!isset($doneList[$dep])) {
                        // there is a dependency that is not met:
                        $resolved = false;
                        break;
                    }
                }

                if($resolved) {
                    //all dependencies are met:
                    $doneList[$item['id']] = true;
                    $res[] = $item;
                    $doneSomething = true;
                }
            }

            if(!$doneSomething) {
                throw new \LogicException('Found unresolved dependency in import. Cannot process further. Pleas check Your dependencies and try import again.');
            }
        }

        return $res;
    }

    /**
     * @param ObjectData[] $objects
     */
    private function findObject(array $objects, string $id): ObjectData
    {
        foreach ($objects as $object) {
            if ($object['@id'] === $id) {
                return $object;
            }
        }

        throw new \RuntimeException(sprintf('Cannot find object %s.', $id));
    }
}
