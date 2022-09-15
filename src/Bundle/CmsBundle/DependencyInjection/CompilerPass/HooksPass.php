<?php

declare(strict_types=1);

namespace Tulia\Bundle\CmsBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Tulia\Component\Hooks\Hooks;
use Tulia\Component\Hooks\HooksSubscriberInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class HooksPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $hooks = $container->getDefinition(Hooks::class);
        $locateableServices = [];

        /**
         * @var HooksSubscriberInterface $id
         */
        foreach ($container->findTaggedServiceIds('hooks.subscriber') as $id => $tags) {
            foreach ($id::getSubscribedActions() as $action => $details) {
                if (is_array($details)) {
                    [$method, $priority] = $details;
                } else {
                    $method = $details;
                    $priority = 0;
                }

                /** @var string $id */
                if (!method_exists($id, $method)) {
                    throw new \BadMethodCallException(sprintf('Method %s of class %s does not exists.', $method, $id));
                }

                $locateableServices[$id] = new Reference($id);

                $hooks->addMethodCall('addAction', [$action, $id.'::'.$method, $priority]);
            }
        }

        $hooks->setArgument(0, ServiceLocatorTagPass::register($container, $locateableServices));
    }
}
