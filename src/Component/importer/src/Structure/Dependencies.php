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
     * @param ObjectData[] $objects
     * @return ObjectData[]
     */
    private function findDependencies(ObjectData $object, array $objects): array
    {
        $dependencies = [[]];
        $regex = '#\[\[@([a-zA-Z]+):([0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12})]]#m';

        foreach ($object->getDefinition()->getFields() as $field) {
            if ($field->isCollection()) {
                foreach ($object[$field->getName()] as $item) {
                    $dependencies[] = $this->findDependencies($item, $objects);
                }
            } else {
                if (false === is_string($object[$field->getName()])) {
                    continue;
                }

                $result = preg_match($regex, $object[$field->getName()], $matches);

                if (!$result) {
                    continue;
                }

                $dependencies[][] = [
                    'placeholder' => $matches[0],
                    '@type' => $matches[1],
                    '@id' => $matches[2],
                    '@handle' => new ObjectPropertyAccessor($object, $field->getName(), $matches[0]),
                ];
            }
        }

        return array_merge(...$dependencies);
    }
}
