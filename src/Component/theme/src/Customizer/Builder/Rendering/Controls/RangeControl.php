<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Builder\Rendering\Controls;

/**
 * @author Adam Banaszkiewicz
 */
class RangeControl extends AbstractControl
{
    public function build(array $params): string
    {
        return '<div class="form-group mb-3' . ($params['is_multilingual'] ? ' form-group-multilingual' : '') . '">
            <label class="customizer-label">' . $this->trans($params['label'], [], $params['translation_domain']) . '</label>
            <input type="range" min="' . ($params['min'] ?? null) . '" max="' . ($params['max'] ?? null) . '" step="' . ($params['step'] ?? null) . '" id="' . $params['control_id'] . '" name="' . $params['control_name'] . '" class="customizer-control form-range" value="' . $this->escapeAttribute($params['value']) . '" data-transport="' . $params['transport'] . '" />
        </div>';
    }

    public static function getName(): string
    {
        return 'range';
    }
}
