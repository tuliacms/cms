<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\Structure;

/**
 * @author Adam Banaszkiewicz
 */
final class Dependencies
{
    /**
     * @param ObjectData[] $objects
     * @return ObjectData[]
     */
    public function fetchDependencies(array $objects): array
    {
        foreach ($objects as $object) {
            $object['@@dependencies'] = $this->findDependencies($object, $objects);
        }

        return $objects;
    }

    /**
     * @return ObjectData[]
     */
    private function findDependencies(ObjectData $object, array $objects): array
    {
        $dependencies = [[]];
        $regex = '#\[\[@([a-zA-Z]+):([0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12})]]#m';

        foreach ($object->getDefinition()->getFields() as $field) {
            if ($field->isCollection()) {
                foreach ($object[$field->getName()] as $item) {
                    $item['@@dependencies'] = $dependencies[] = $this->findDependencies($item, $objects);
                }
            } else {
                if (false === is_string($object[$field->getName()])) {
                    continue;
                }

                $result = preg_match_all($regex, $object[$field->getName()], $matches);

                if (!$result) {
                    continue;
                }

                $matches = $this->rebuildMatches($matches);

                foreach ($matches as $key => $match) {
                    $dependencies[][] = [
                        'placeholder' => $matches[$key][0],
                        '@type' => $matches[$key][1],
                        '@id' => $matches[$key][2],
                        '@handle' => new ObjectPropertyAccessor($object, $field->getName(), $matches[$key][0]),
                    ];
                }
            }
        }

        return array_merge(...$dependencies);
    }

    private function rebuildMatches(array $matches): array
    {
        $result = [];

        foreach ($matches as $mk => $group) {
            foreach ($group as $gk => $val) {
                $result[$gk][$mk] = $val;
            }
        }

        return $result;
    }
}
