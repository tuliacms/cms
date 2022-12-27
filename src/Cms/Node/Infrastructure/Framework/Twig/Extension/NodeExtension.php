<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Framework\Twig\Extension;

use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RouterInterface;
use Tulia\Cms\Node\Domain\ReadModel\Finder\NodeFinderInterface;
use Tulia\Cms\Node\Domain\ReadModel\Finder\NodeFinderScopeEnum;
use Tulia\Cms\Node\Domain\ReadModel\Model\Node;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class NodeExtension extends AbstractExtension
{
    public function __construct(
        private readonly RouterInterface $router,
        private readonly NodeFinderInterface $nodeFinder,
        private readonly WebsiteInterface $website,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('node_path', function (Node $node, array $parameters = []) {
                return $this->generate($node, $parameters, RouterInterface::ABSOLUTE_PATH);
            }, [
                'is_safe' => [ 'html' ]
            ]),
            new TwigFunction('node_path_from_id', function (string $nodeId, array $parameters = []) {
                $node = $this->nodeFinder->findOne([
                    'id' => $nodeId,
                    'locale' => $this->website->getLocale()->getCode(),
                    'website_id' => $this->website->getId(),
                ], NodeFinderScopeEnum::ROUTING_GENERATOR);

                if (!$node) {
                    return '';
                }

                return $this->generate($node, $parameters, RouterInterface::ABSOLUTE_PATH);
            }, [
                'is_safe' => [ 'html' ]
            ]),
            new TwigFunction('node_url', function (Node $node, array $parameters = []) {
                return $this->generate($node, $parameters, RouterInterface::ABSOLUTE_PATH);
            }, [
                'is_safe' => [ 'html' ]
            ]),
            new TwigFunction('node_url_from_id', function (string $nodeId, array $parameters = []) {
                $node = $this->nodeFinder->findOne([
                    'id' => $nodeId,
                    'locale' => $this->website->getLocale()->getCode(),
                    'website_id' => $this->website->getId(),
                ], NodeFinderScopeEnum::ROUTING_GENERATOR);

                if (!$node) {
                    return '';
                }

                return $this->generate($node, $parameters, RouterInterface::ABSOLUTE_PATH);
            }, [
                'is_safe' => [ 'html' ]
            ]),
            new TwigFunction('find_nodes', function (array $parameters) {
                if (!isset($parameters['locale'])) {
                    $parameters['locale'] = $this->website->getLocale()->getCode();
                }
                if (!isset($parameters['website_id'])) {
                    $parameters['website_id'] = $this->website->getLocale()->getCode();
                }
                return $this->nodeFinder->find($parameters, NodeFinderScopeEnum::LISTING);
            }, [
                'is_safe' => [ 'html' ]
            ]),
        ];
    }

    private function generate(Node $node, array $parameters, int $type): ?string
    {
        try {
            $parameters['_node_instance'] = $node;

            return $this->router->generate(
                sprintf('frontend.node.%s.%s', $node->getType(), $node->getId()),
                $parameters,
                $type
            );
        } catch (RouteNotFoundException $exception) {
            return '';
        }
    }
}
