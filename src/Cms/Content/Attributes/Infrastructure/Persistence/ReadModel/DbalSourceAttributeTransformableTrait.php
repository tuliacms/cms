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
            $result[$row['uri']] = [
                'value' => json_decode((string) $row['value_values'], true, 2, JSON_THROW_ON_ERROR),
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
