<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Attributes\Domain\ReadModel\Service;

use Tulia\Cms\ContentBuilder\Attributes\Domain\ReadModel\ValueRender\ValueRendererInterface;

/**
 * @author Adam Banaszkiewicz
 */
class AttributesValueRenderer
{
    private ValueRendererInterface $valueRenderer;
    /** @var string[] */
    private array $scopesByType;

    public function __construct(ValueRendererInterface $valueRenderer, array $scopes)
    {
        $this->valueRenderer = $valueRenderer;
        $this->scopesByType = $scopes;
    }

    public function renderValues(array $attributes, string $type, string $scope): array
    {
        if ($attributes === []) {
            return $attributes;
        }

        if (\in_array($scope, $this->scopesByType[$type]['scopes'] ?? [], true) === false) {
            return $this->extractValue($attributes);
        }

        foreach ($attributes as $key => $attribute) {
            if ($attribute['is_renderable']) {
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
