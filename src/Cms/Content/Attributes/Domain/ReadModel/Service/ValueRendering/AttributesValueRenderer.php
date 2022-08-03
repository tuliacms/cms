<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Attributes\Domain\ReadModel\Service\ValueRendering;

/**
 * @author Adam Banaszkiewicz
 */
class AttributesValueRenderer
{
    private ValueRendererInterface $valueRenderer;

    public function __construct(ValueRendererInterface $valueRenderer)
    {
        $this->valueRenderer = $valueRenderer;
    }

    public function renderValues(array $attributes): array
    {
        if ($attributes === []) {
            return $attributes;
        }

        foreach ($attributes as $key => $attribute) {
            if (in_array('renderable', $attribute['flags'], true)) {
                $attributes[$key]['value'] = $this->valueRenderer->render($attribute['compiled_value'], [
                    'attribute' => $key
                ]);
            }
        }

        return $this->extractValue($attributes);
    }

    private function extractValue(array $attributes): array
    {
        foreach ($attributes as $key => $attribute) {
            $attributes[$key] = $attribute['value'];
        }

        return $attributes;
    }
}
