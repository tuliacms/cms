<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Infrastructure\Persistence\ContainerProvider;

use Tulia\Cms\Content\Type\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeProviderInterface;
use Tulia\Cms\Content\Type\Infrastructure\Persistence\ContentProvider\SymfonyContainerStandarizableTrait;

/**
 * @author Adam Banaszkiewicz
 */
class WidgetTypeContainerProvider implements ContentTypeProviderInterface
{
    use SymfonyContainerStandarizableTrait;

    public function __construct(
        private array $widgets,
        private array $configuration
    ) {
        $this->widgets = $widgets;
        $this->configuration = $configuration;
    }

    public function provide(): array
    {
        $result = [];

        foreach ($this->widgets as $code => $widget) {
            $type = $this->configuration['widget'];
            $type['layout']['sections']['main']['groups']['widget_options']['fields'] = $widget['fields'];
            $type['code'] = 'widget_' . str_replace('.', '_', $code);
            $type['internal'] = true;
            $type = $this->standarizeArray($type);

            $result[] = ContentType::fromArray($type);
        }

        return $result;
    }
}
