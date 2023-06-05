<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Builder\Rendering\Controls;

/**
 * @author Adam Banaszkiewicz
 */
class Registry implements RegistryInterface
{
    /**
     * @var ControlInterface[]
     */
    protected $controls;

    public function __construct(iterable $controls)
    {
        $this->controls = $controls;
    }

    public function build(string $id, string $type, array $params): ?string
    {
        $params['control_name'] = $id;
        $params['control_id']   = $this->createControlId($params['code']);
        $names = [];

        foreach ($this->controls as $control) {
            if ($control::getName() === $type) {
                return $control->build($params);
            }

            $names[] = $control::getName();
        }

        throw new \OutOfBoundsException(sprintf(
            'Control type "%s" not registered, choose one of the following: %s.',
            $type,
            implode(', ', $names)
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function createControlId($input): string
    {
        return 'control-' . preg_replace('/[^a-zA-Z0-9\-_]+/i', '-', $input);
    }
}
