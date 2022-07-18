<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Infrastructure\Persistence\ContainerProvider;

use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeProviderInterface;
use Tulia\Cms\Content\Type\Infrastructure\Persistence\ContentProvider\SymfonyContainerStandarizableTrait;

/**
 * @author Adam Banaszkiewicz
 */
class WidgetTypeContainerProvider implements ContentTypeProviderInterface
{
    use SymfonyContainerStandarizableTrait;

    private array $widgets;
    private array $configuration;

    public function __construct(array $widgets, array $configuration)
    {
        $this->widgets = $widgets;
        $this->configuration = $configuration;
    }

    public function provide(): array
    {
        return [];
        $result = [];

        foreach ($this->widgets as $code => $widget) {
            $type = $this->configuration['widget'];
            $type['layout']['sections']['main']['groups']['widget_options']['fields'] = $widget['fields'];
            $type['code'] = 'widget_' . str_replace('.', '_', $code);
            $type['internal'] = true;
            $type = $this->standarizeArray($type);

            $result[] = $this->buildFromArray($type);
        }

        return $result;
    }
}
