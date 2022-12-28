<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Cms\Options;

use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Options\Domain\WriteModel\Service\OptionsProviderInterface;

/**
 * @author Adam Banaszkiewicz
 * @final
 * @lazy
 */
class NodeTypeOptionsProvider implements OptionsProviderInterface
{
    public function __construct(
        private readonly ContentTypeRegistryInterface $contentTypeRegistry,
    ) {
    }

    public function provide(): array
    {
        $options = [
            'category_taxonomy' => [
                'value' => null,
                'multilingual' => false,
                'autoload' => true,
            ],
        ];

        $result = [];

        foreach ($this->contentTypeRegistry->allByType('node') as $contentType) {
            foreach ($options as $name => $option) {
                $name = sprintf('node.%s.%s', $contentType->getCode(), $name);

                $result[$name] = $option;
            }
        }

        return $result;
    }
}
