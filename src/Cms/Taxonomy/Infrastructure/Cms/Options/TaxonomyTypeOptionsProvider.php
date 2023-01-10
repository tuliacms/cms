<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Cms\Options;

use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Options\Domain\WriteModel\Service\OptionsProviderInterface;

/**
 * @author Adam Banaszkiewicz
 * @final
 * @lazy
 */
class TaxonomyTypeOptionsProvider implements OptionsProviderInterface
{
    public function __construct(
        private readonly ContentTypeRegistryInterface $contentTypeRegistry,
    ) {
    }

    public function provide(): array
    {
        $options = [
            'nodes_per_page' => [
                'value' => 9,
                'multilingual' => false,
                'autoload' => true,
            ],
        ];

        $result = [];

        foreach ($this->contentTypeRegistry->allByType('taxonomy') as $contentType) {
            foreach ($options as $name => $option) {
                $name = sprintf('taxonomy.%s.%s', $contentType->getCode(), $name);

                $result[$name] = $option;
            }
        }

        return $result;
    }
}
