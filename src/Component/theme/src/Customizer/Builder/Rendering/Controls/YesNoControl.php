<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Builder\Rendering\Controls;

/**
 * @author Adam Banaszkiewicz
 */
class YesNoControl extends SelectControl
{
    public function build(array $params): string
    {
        $params['choices'] = $params['choices'] ?? [
            'yes' => $this->trans('yes'),
            'no' => $this->trans('no'),
        ];

        return parent::build($params);
    }

    /**
     * {@inheritdoc}
     */
    public static function getName(): string
    {
        return 'yes_no';
    }
}
