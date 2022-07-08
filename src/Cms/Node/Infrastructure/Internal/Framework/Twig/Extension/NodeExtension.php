<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Internal\Framework\Twig\Extension;

use Symfony\Component\Routing\RouterInterface;
use Tulia\Cms\Node\Domain\ReadModel\Finder\NodeFinderInterface;
use Tulia\Cms\Node\Domain\ReadModel\Finder\NodeFinderScopeEnum;
use Tulia\Cms\Node\Domain\ReadModel\Model\Node;
use Tulia\Component\Routing\Exception\RoutingException;
use Tulia\Component\Routing\Website\WebsiteInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class NodeExtension extends AbstractExtension
{
    public function __construct(
        private RouterInterface $router,
        private NodeFinderInterface $nodeFinder,
        private WebsiteInterface $website
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
                ], NodeFinderScopeEnum::ROUTING_GENERATOR);

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
                ], NodeFinderScopeEnum::ROUTING_GENERATOR);

                return $this->generate($node, $parameters, RouterInterface::ABSOLUTE_PATH);
            }, [
                'is_safe' => [ 'html' ]
            ]),
            new TwigFunction('find_nodes', function (array $parameters) {
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
                sprintf('node.%s.%s', $node->getType(), $node->getId()),
                $parameters,
                $type
            );
        } catch (RoutingException $exception) {
            return '';
        }
    }
}
