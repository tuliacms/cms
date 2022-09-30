<?php

declare(strict_types=1);

namespace Tulia\Bundle\CmsBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Theme\Domain\WriteModel\Service\ThemeImportCollectionRegistry;
use Tulia\Component\Theme\Configuration\Configuration;
use Tulia\Component\Theme\Configuration\ConfigurationRegistry;
use Tulia\Component\Theme\Customizer\Builder\Structure\StructureRegistry;
use Tulia\Component\Theme\Customizer\Changeset\PredefinedChangesetRegistry;

/**
 * @author Adam Banaszkiewicz
 */
class ThemePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        foreach ($container->findTaggedServiceIds('theme.customizer.control') as $id => $tags) {
            $container->getDefinition($id)->addMethodCall('setTranslator', [new Reference(TranslatorInterface::class)]);
        }

        $configurationRegistry = $container->getDefinition(ConfigurationRegistry::class);
        $structureRegistry = $container->getDefinition(StructureRegistry::class);
        $predefinedChangesetsRegistry = $container->getDefinition(PredefinedChangesetRegistry::class);
        $themeImportCollectionRegistry = $container->getDefinition(ThemeImportCollectionRegistry::class);

        foreach ($container->getParameter('cms.themes.configuration') as $theme => $config) {
            if (isset($config['configuration']['base'])) {
                $this->processThemeConfiguration($container, $configurationRegistry, 'base', $theme, $config['configuration']['base'], $config['translation_domain'], $config['css_framework']);
            }
            if (isset($config['configuration']['customizer'])) {
                $this->processThemeConfiguration($container, $configurationRegistry, 'customizer', $theme, $config['configuration']['customizer'], $config['translation_domain'], $config['css_framework']);
            }
            if (isset($config['imports']['collections'])) {
                $this->processThemeImports($container, $themeImportCollectionRegistry, $theme, $config['imports']['collections']);
            }
            if (isset($config['customizer']['builder'])) {
                $structureRegistry->addMethodCall('addForTheme', [$theme, $this->resolveCustomizerStructure($config['customizer']['builder'], $config['translation_domain'])]);
            }
            if (isset($config['customizer']['changesets'])) {
                foreach ($config['customizer']['changesets'] as $key => $changeset) {
                    $config['customizer']['changesets'][$key]['translation_domain'] = $config['translation_domain'];
                }

                $predefinedChangesetsRegistry->addMethodCall('addForTheme', [$theme, $config['customizer']['changesets']]);
            }
        }
    }

    private function processThemeConfiguration(
        ContainerBuilder $container,
        Definition $registry,
        string $group,
        string $theme,
        array $config,
        string $translationDomain,
        string $cssFramework
    ): void {
        $service = new Definition(Configuration::class);
        $service->setShared(true);
        $service->addMethodCall('setTranslationDomain', [$translationDomain]);
        $service->addMethodCall('setCssFramework', [$cssFramework]);

        if (isset($config['assets'])) {
            foreach ($config['assets'] as $asset) {
                $service->addMethodCall('addAsset', [$asset]);
            }
        }
        if (isset($config['widget_spaces'])) {
            foreach ($config['widget_spaces'] as $space) {
                $service->addMethodCall('addWidgetSpace', [$space['name'], $space['label']]);
            }
        }
        if (isset($config['widget_styles'])) {
            foreach ($config['widget_styles'] as $style) {
                $service->addMethodCall('addWidgetStyle', [$style['name'], $style['label']]);
            }
        }
        if (isset($config['image_sizes'])) {
            foreach ($config['image_sizes'] as $size) {
                $service->addMethodCall('addImageSize', [$size['name'], $size['width'], $size['height'], $size['mode']]);
            }
        }
        if (isset($config['node_content_field'])) {
            $service->addMethodCall('setNodeContentField', [$config['node_content_field']]);
        }

        $serviceName = sprintf('theme.configuration.%s.%s', $theme, $group);

        $container->setDefinition($serviceName, $service);
        $registry->addMethodCall('addConfiguration', [$theme, $group, new Reference($serviceName)]);
    }

    private function resolveCustomizerStructure(array $source, string $translationDomain): array
    {
        $structure = [];

        foreach ($source as $code => $section) {
            $controls = [];

            foreach ($section['controls'] as $controlCode => $control) {
                $control['code'] = $controlCode;
                $control['options'] = $control['control_options'];

                unset($control['control_options']);

                $controls[] = $control;
            }

            $section['code'] = $code;
            $section['translation_domain'] = $translationDomain;
            $section['controls'] = $controls;

            $structure[] = $section;
        }

        return $structure;
    }

    private function processThemeImports(ContainerBuilder $container, Definition $registry, string $theme, array $imports): void
    {
        foreach ($imports as $code => $info) {
            $registry->addMethodCall('addCollection', [$theme, $code, $info]);
        }
    }
}
