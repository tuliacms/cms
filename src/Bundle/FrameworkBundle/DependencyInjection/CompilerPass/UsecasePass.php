<?php

declare(strict_types=1);

namespace Tulia\Bundle\FrameworkBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Tulia\Cms\Shared\Application\UseCase\TransactionalSessionInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class UsecasePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        foreach ($container->findTaggedServiceIds('usecase.transactional') as $id => $tags) {
            $container->getDefinition($id)->addMethodCall('setTransactionalSession', [new Reference(TransactionalSessionInterface::class)]);
        }
    }
}
