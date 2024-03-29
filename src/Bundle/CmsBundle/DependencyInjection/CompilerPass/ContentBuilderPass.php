<?php

declare(strict_types=1);

namespace Tulia\Bundle\CmsBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Tulia\Cms\Content\Type\Domain\ReadModel\FieldChoicesProvider\FieldChoicesProviderRegistry;
use Tulia\Cms\Content\Type\Domain\ReadModel\FieldTypeBuilder\FieldTypeBuilderRegistry;
use Tulia\Cms\Content\Type\Domain\ReadModel\FieldTypeHandler\FieldTypeHandlerRegistry;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ConstraintTypeMappingRegistry;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeDecorator;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\FieldTypeMappingRegistry;
use Tulia\Cms\Content\Type\Domain\WriteModel\Routing\Strategy\ContentTypeRoutingStrategyRegistry;
use Tulia\Cms\Content\Type\Infrastructure\Framework\Form\Service\LayoutTypeBuilderRegistry;

/**
 * @author Adam Banaszkiewicz
 */
class ContentBuilderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $this->addTaggedServices($container, ContentTypeRegistryInterface::class, 'content_builder.content_type.provider', 'addProvider');
        $this->addTaggedServices($container, ContentTypeDecorator::class, 'content_builder.content_type.decorator', 'addDecorator');
        $this->addTaggedServices($container, LayoutTypeBuilderRegistry::class, 'content_builder.layout_type.builder', 'addBuilder');
        $this->addTaggedServices($container, ContentTypeRoutingStrategyRegistry::class, 'content_builder.content_type.routing_strategy', 'addStrategy');
        $this->addTaggedServicesById($container, FieldChoicesProviderRegistry::class, 'content_builder.data_types.configuration.choices_provider', 'addProvider');
        $this->addTaggedServicesById($container, FieldTypeBuilderRegistry::class, 'content_builder.data_types.builder', 'addBuilder');
        $this->addTaggedServicesById($container, FieldTypeHandlerRegistry::class, 'content_builder.data_types.handler', 'addHandler');

        $registry = $container->getDefinition(FieldTypeMappingRegistry::class);
        foreach ($container->getParameter('cms.content_builder.data_types.mapping') as $type => $info) {
            $registry->addMethodCall('addMapping', [$type, $info]);
        }

        $registry = $container->getDefinition(ConstraintTypeMappingRegistry::class);
        foreach ($container->getParameter('cms.content_builder.constraint_types.mapping') as $type => $info) {
            $registry->addMethodCall('addMapping', [$type, $info]);
        }
    }

    private function addTaggedServices(ContainerBuilder $container, string $serviceId, string $tagname, string $method)
    {
        $registry = $container->getDefinition($serviceId);

        foreach ($container->findTaggedServiceIds($tagname) as $id => $options) {
            if ($container->getDefinition($id)->isAbstract()) {
                continue;
            }

            $registry->addMethodCall($method, [new Reference($id)]);
        }
    }

    private function addTaggedServicesById(ContainerBuilder $container, string $serviceId, string $tagname, string $method)
    {
        $registry = $container->getDefinition($serviceId);

        foreach ($container->findTaggedServiceIds($tagname) as $id => $options) {
            if ($container->getDefinition($id)->isAbstract()) {
                continue;
            }

            $registry->addMethodCall($method, [$id, new Reference($id)]);
        }
    }
}
