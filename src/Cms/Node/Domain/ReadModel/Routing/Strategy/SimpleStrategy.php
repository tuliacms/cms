<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\ReadModel\Routing\Strategy;

use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Content\Type\Domain\WriteModel\Routing\Router;
use Tulia\Cms\Content\Type\Domain\WriteModel\Routing\Strategy\ContentTypeRoutingStrategyInterface;
use Tulia\Cms\Node\Domain\ReadModel\Finder\NodeFinderInterface;
use Tulia\Cms\Node\Domain\ReadModel\Finder\NodeFinderScopeEnum;
use Tulia\Cms\Node\Domain\ReadModel\Model\Node;

/**
 * @author Adam Banaszkiewicz
 */
class SimpleStrategy implements ContentTypeRoutingStrategyInterface
{
    public function __construct(
        private readonly NodeFinderInterface $nodeFinder,
        private readonly ContentTypeRegistryInterface $contentTypeRegistry,
        private readonly Router $contentTypeRouter,
    ) {
    }

    public function generate(string $id, array $parameters = []): string
    {
        if (isset($parameters['_node_instance'])) {
            return '/' . $parameters['_node_instance']->getSlug();
        }

        if (!$parameters['_locale']) {
            throw new \InvalidArgumentException('Please provide locale.');
        }

        $node = $this->findNodeById($id, $parameters['_website'], $parameters['_locale']);

        if ($node) {
            return '/' . $node->getSlug();
        }

        return '';
    }

    public function match(string $pathinfo, array $parameters = []): array
    {
        $node = $this->findNodeBySlug(
            substr($pathinfo, 1),
            $parameters['_website'],
            $parameters['_locale']
        );

        if (! $node) {
            return [];
        }

        $nodeType = $this->contentTypeRegistry->get($node->getType());

        if (! $nodeType || $nodeType->isType('node') === false || $nodeType->isRoutable() === false) {
            return [];
        }

        return [
            'node' => $node,
            'slug' => $node->getSlug(),
            '_route' => sprintf('frontend.node.%s.%s', $node->getType(), $node->getId()),
            '_controller' => $nodeType->getController(),
        ];
    }

    public function supports(string $contentType): bool
    {
        return $contentType === 'node';
    }

    public function getId(): string
    {
        return 'simple';
    }

    private function findNodeBySlug(?string $slug, string $websiteId, string $locale): ?Node
    {
        return $this->nodeFinder->findOne([
            'slug'      => $slug,
            'per_page'  => 1,
            'order_by'  => null,
            'order_dir' => null,
            'locale'    => $locale,
            'website_id'=> $websiteId,
        ], NodeFinderScopeEnum::ROUTING_MATCHER);
    }

    private function findNodeById(?string $id, string $websiteId, string $locale): ?Node
    {
        return $this->nodeFinder->findOne([
            'id' => $id,
            'locale' => $locale,
            'website_id'=> $websiteId,
        ], NodeFinderScopeEnum::ROUTING_MATCHER);
    }
}
