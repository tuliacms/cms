<?php

declare(strict_types=1);

namespace Tulia\Bundle\CmsBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Adam Banaszkiewicz
 */
final class CachePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $definition = $container->getDefinition('cache.adapter.doctrine_dbal');
        $definition->setArgument(3, [
            'db_table' => '#__cache_items'
        ]);
        $definition = $container->getDefinition('cache.adapter.pdo');
        $definition->setArgument(3, [
            'db_table' => '#__cache_items'
        ]);
    }
}
