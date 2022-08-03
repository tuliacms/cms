<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Attributes\Infrastructure\Persistence\ReadModel;

/**
 * @author Adam Banaszkiewicz
 */
trait DbalSourceAttributeTransformableTrait
{
    public function transformSource(array $source): array
    {
        $result = [];

        foreach ($source as $row) {
            /*if ($row['has_nonscalar_value']) {
                try {
                    $row['value'] = (array) unserialize(
                        (string) $row['value'],
                        ['allowed_classes' => []]
                    );
                } catch (\ErrorException $e) {
                    // If error, than empty or cannot be unserialized from singular value
                }
            }*/

            $result[$row['uri']] = [
                'value' => $row['value'],
                'compiled_value' => $row['compiled_value'],
                'payload' => $row['payload'] ? unserialize($row['payload'], ['allowed_classess' => []]) : [],
                'flags' => $row['flags'] ? unserialize($row['flags'], ['allowed_classess' => []]) : [],
                'uri' => $row['uri'],
                'code' => $row['code'],
            ];
        }

        return $result;
    }
}
