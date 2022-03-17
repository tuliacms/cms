<?php

declare(strict_types=1);

namespace Tulia\Cms\Attributes\Domain\WriteModel\Service;

use Tulia\Cms\Attributes\Domain\WriteModel\Model\Attribute;

/**
 * @author Adam Banaszkiewicz
 */
class UriToArrayTransformer
{
    /**
     * @param Attribute[] $attributes
     * @return array
     */
    public function transform(array $attributes): array
    {
        $output = [];

        foreach ($attributes as $uri => $attribute) {
            parse_str($uri.'=v', $result);

            $attribute = $this->assignValueToMostDeepIndex($result, $attribute);
            $output = $this->mergeRecursive($output, $attribute);
        }

        return $output;
    }

    private function assignValueToMostDeepIndex(&$input, $value)
    {
        if (is_array($input)) {
            foreach ($input as &$item) {
                $this->assignValueToMostDeepIndex($item, $value);
            }

            unset($item);
        }

        if ($input === 'v') {
            $input = $value;
        }

        return $input;
    }

    private function mergeRecursive(&$target, $value)
    {
        foreach ($value as $key => &$item) {
            if (is_array($item)) {
                if (isset($target[$key]) === false) {
                    $target[$key] = [];
                }

                $target[$key] = $this->mergeRecursive($target[$key], $item);
            } else {
                $target[$key] = $item;
            }
        }

        return $this->sortArrayByKey($target);
    }

    private function sortArrayByKey(array $array): array
    {
        ksort($array);

        return $array;
    }
}
