<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\WriteModel\Routing;

use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Content\Type\Domain\WriteModel\Routing\Strategy\ContentTypeRoutingStrategyRegistry;

/**
 * @author Adam Banaszkiewicz
 */
class Router
{
    private ContentTypeRoutingStrategyRegistry $strategyRegistry;
    private ContentTypeRegistryInterface $contentTypeRegistry;

    public function __construct(
        ContentTypeRoutingStrategyRegistry $strategyRegistry,
        ContentTypeRegistryInterface $contentTypeRegistry
    ) {
        $this->strategyRegistry = $strategyRegistry;
        $this->contentTypeRegistry = $contentTypeRegistry;
    }

    public function generate(string $contentTypeCode, string $id, array $parameters = []): ?string
    {
        $contentType = $this->contentTypeRegistry->get($contentTypeCode);

        return $this->strategyRegistry->getByContentType($contentType)->generate($id, $parameters);
    }

    public function match(string $pathinfo, array $parameters = []): array
    {
        foreach ($this->strategyRegistry->all() as $strategy) {
            $routingParameters = $strategy->match($pathinfo, $parameters);

            if ($routingParameters !== []) {
                return $routingParameters;
            }
        }

        return [];
    }
}