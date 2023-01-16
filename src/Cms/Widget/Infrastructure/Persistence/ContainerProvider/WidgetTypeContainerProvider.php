<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Infrastructure\Persistence\ContainerProvider;

use Tulia\Cms\Content\Type\Domain\ReadModel\ContentTypeBuilder\ContentTypeCollector;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeProviderInterface;
use Tulia\Cms\Content\Type\Infrastructure\Persistence\ContentProvider\SymfonyContainerStandarizableTrait;

/**
 * @author Adam Banaszkiewicz
 */
class WidgetTypeContainerProvider implements ContentTypeProviderInterface
{
    use SymfonyContainerStandarizableTrait;

    public function __construct(
        private readonly array $widgets,
        private readonly array $configuration,
    ) {
    }

    public function provide(ContentTypeCollector $collector): void
    {
        foreach ($this->widgets as $code => $widget) {
            $type = $this->configuration['widget'];
            $type['layout']['sections']['main']['groups']['widget_options']['fields'] = $widget['fields'];
            $type['code'] = 'widget_' . str_replace('.', '_', $code);
            $type = $this->standarizeArray($type);

            $typeDef = $collector->newOne($type['type'], $type['code'], $type['name']);
            $typeDef->icon = $type['icon'];
            $typeDef->isInternal = true;

            foreach ($type['fields_groups'] as $group) {
                $groupDef = $typeDef->fieldsGroup($group['code'], $group['name'], $group['section'], $group['active'] ?? false);

                foreach ($group['fields'] as $fieldCode => $fieldArray) {
                    $groupDef->fieldFromArray($fieldCode, $fieldArray);
                }
            }
        }
    }
}
