<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Infrastructure\Persistence\ContentProvider;

use Tulia\Cms\Content\Type\Domain\ReadModel\ContentTypeBuilder\ContentTypeCollector;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeProviderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ContentTypeContainerProvider implements ContentTypeProviderInterface
{
    use SymfonyContainerStandarizableTrait;

    public function __construct(
        private readonly array $configuration,
    ) {
    }

    public function provide(ContentTypeCollector $collector): void
    {
        foreach ($this->configuration as $code => $type) {
            $type['code'] = $code;
            $type['internal'] = true;
            $type = $this->standarizeArray($type);

            $typeDef = $collector->newOne($type['type'], $code, $type['name']);
            $typeDef->icon = $type['icon'];
            $typeDef->isRoutable = (bool) $type['is_routable'];
            $typeDef->isHierarchical = (bool) $type['is_hierarchical'];
            $typeDef->routingStrategy = $type['routing_strategy'];
            $typeDef->controller = $type['controller'];
            $typeDef->isInternal = $type['is_internal'] ?? false;

            foreach ($type['fields_groups'] as $group) {
                $groupDef = $typeDef->fieldsGroup($group['code'], $group['name'], $group['section']);

                foreach ($group['fields'] as $fieldCode => $fieldArray) {
                    $groupDef->fieldFromArray($fieldCode, $fieldArray);
                }
            }
        }
    }
}
