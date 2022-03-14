<?php

declare(strict_types=1);

namespace Tulia\Bundle\FrameworkBundle\DependencyInjection;

use Symfony\Bundle\FrameworkBundle\DependencyInjection\FrameworkExtension;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Tulia\Component\Templating\ViewFilter\FilterInterface;

use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

/**
 * @author Adam Banaszkiewicz
 */
class TuliaCmsExtension extends FrameworkExtension
{
    public function getAlias(): string
    {
        return 'framework';
    }

    public function getConfiguration(array $config, ContainerBuilder $container): ?ConfigurationInterface
    {
        return new Configuration($container->getParameter('kernel.debug'));
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        parent::load($configs, $container);

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('framework.assetter.assets', $config['assetter']['assets'] ?? []);
        $container->setParameter('framework.assets.public_paths', $config['public_paths'] ?? []);
        $container->setParameter('framework.twig.loader.array.templates', $this->prepareTwigArrayLoaderTemplates($config['twig']['loader']['array']['templates'] ?? []));
        $container->setParameter('framework.templating.paths', $this->prepareTemplatingPaths($config['templating']['paths'] ?? []));
        $container->setParameter('framework.templating.namespace_overwrite', $config['templating']['namespace_overwrite'] ?? []);
        $container->setParameter('framework.themes.configuration', $config['theme']['configuration'] ?? []);

        $this->registerViewFilters($container);

        // Finders
        $container->registerForAutoconfiguration(\Tulia\Cms\Shared\Domain\ReadModel\Finder\AbstractFinder::class)
            ->addTag('DbalFinder');
        $container->registerForAutoconfiguration(\Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\Plugin\PluginInterface::class)
            ->addTag('finder.plugin');
        // Themes
        $container->registerForAutoconfiguration(\Tulia\Component\Theme\Resolver\ResolverInterface::class)
            ->addTag('theme.resolver');
        $container->registerForAutoconfiguration(\Tulia\Component\Theme\Customizer\Builder\Rendering\Controls\ControlInterface::class)
            ->addTag('theme.customizer.control');
        // Import/Export
        $container->registerForAutoconfiguration(\Tulia\Component\Importer\ObjectImporter\ObjectImporterInterface::class)
            ->addTag('importer.object_importer');

        $container->getDefinition('cache.adapter.pdo')->replaceArgument(3, ['db_table' => sprintf('%scache_pools', env('DATABASE_PREFIX'))]);
        $container->getDefinition('cache.adapter.doctrine_dbal')->replaceArgument(3, ['db_table' => sprintf('%scache_pools', env('DATABASE_PREFIX'))]);
    }

    private function prepareTemplatingPaths(array $paths): array
    {
        $prepared = [];

        foreach ($paths as $path) {
            $prepared["@{$path['name']}"] = $path['path'];
        }

        return $prepared;
    }

    private function prepareTwigArrayLoaderTemplates(array $source): array
    {
        $output = [];

        foreach ($source as $name => $data) {
            $output[$name] = $data['template'];
        }

        return $output;
    }

    private function registerViewFilters(ContainerBuilder $container): void
    {
        if (! $container->has(FilterInterface::class)) {
            return;
        }

        $chain = $container->findDefinition(FilterInterface::class);

        foreach ($container->findTaggedServiceIds('templating.view_filter') as $id => $tags) {
            $chain->addMethodCall('addFilter', [new Reference($id)]);
        }
    }
}
