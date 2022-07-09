<?php

declare(strict_types=1);

namespace Tulia\Bundle\CmsBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\Plugin\PluginRegistry;

/**
 * @author Adam Banaszkiewicz
 */
class FinderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        foreach ($container->findTaggedServiceIds('finder') as $id => $tags) {
            $definition = $container->getDefinition($id);
            $definition->addMethodCall('setEventBus', [new Reference(EventBusInterface::class)]);
            $definition->addMethodCall('setPluginsRegistry', [new Reference(PluginRegistry::class)]);
        }

        $pluginRegistry = $container->getDefinition(PluginRegistry::class);

        foreach ($container->findTaggedServiceIds('finder.plugin') as $id => $tags) {
            $pluginRegistry->addMethodCall('addPlugin', [new Reference($id)]);
        }
    }
}
