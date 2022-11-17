<?php

declare(strict_types=1);

namespace Tulia\Bundle\CmsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Tulia\Component\Theme\Customizer\Changeset\Changeset;

/**
 * @author Adam Banaszkiewicz
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('cms');
        $root = $treeBuilder->getRootNode();

        $this->registerOptionsConfiguration($root);
        $this->registerContentBuildingConfiguration($root);
        $this->registerContentBlockConfiguration($root);
        $this->registerAttributesConfiguration($root);
        $this->registerWidgetsConfiguration($root);
        $this->registerFilemanagerConfiguration($root);
        $this->registerImporterConfiguration($root);
        $this->registerAssetsConfiguration($root);
        $this->registerAssetterConfiguration($root);
        $this->registerTwigConfiguration($root);
        $this->registerTemplatingConfiguration($root);
        $this->registerThemeConfiguration($root);
        $this->registerHooksConfiguration($root);
        $this->registerSearchAnythingConfiguration($root);

        return $treeBuilder;
    }

    private function registerOptionsConfiguration(ArrayNodeDefinition $root): void
    {
        $root
            ->children()
                ->arrayNode('options')
                    ->children()
                        ->arrayNode('definitions')
                            ->useAttributeAsKey('name')
                            ->arrayPrototype()
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->variableNode('value')->defaultNull()->end()
                                    ->booleanNode('multilingual')->defaultFalse()->end()
                                    ->booleanNode('autoload')->defaultFalse()->end()
                                    ->scalarNode('type')
                                        ->defaultValue('scalar')
                                            ->validate()
                                                ->ifNotInArray(['scalar', 'boolean', 'number', 'array'])
                                                ->thenInvalid('Invalid option type %s. Allowed: scalar, array.')
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function registerAttributesConfiguration(ArrayNodeDefinition $root): void
    {
        $root
            ->children()
                ->arrayNode('attributes')
                    ->children()
                        ->arrayNode('finder')
                            ->children()
                                ->arrayNode('types')
                                    ->arrayPrototype()
                                        ->addDefaultsIfNotSet()
                                        ->children()
                                            ->arrayNode('scopes')->scalarPrototype()->defaultValue([])->end()->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function registerContentBuildingConfiguration(ArrayNodeDefinition $root): void
    {
        $root
            ->children()
                ->arrayNode('content_building')
                    ->children()
                        ->arrayNode('constraint_types')
                            ->children()
                                ->append($this->buildConstraintsNode('mapping'))
                            ->end()
                        ->end()
                        ->arrayNode('data_types')
                            ->children()
                                ->arrayNode('mapping')
                                    ->arrayPrototype()
                                        ->addDefaultsIfNotSet()
                                        ->children()
                                            ->arrayNode('flags')->scalarPrototype()->defaultValue([])->end()->end()
                                            ->scalarNode('label')->isRequired()->end()
                                            ->scalarNode('classname')->isRequired()->end()
                                            ->scalarNode('builder')->defaultNull()->end()
                                            ->scalarNode('handler')->defaultNull()->end()
                                            ->arrayNode('constraints')->scalarPrototype()->defaultValue([])->end()->end()
                                            ->arrayNode('exclude_for_types')->scalarPrototype()->defaultValue([])->end()->end()
                                            ->arrayNode('only_for_types')->scalarPrototype()->defaultValue([])->end()->end()
                                            ->scalarNode('is_multiple')->defaultFalse()->end()
                                            ->arrayNode('configuration')
                                                ->arrayPrototype()
                                                    ->addDefaultsIfNotSet()
                                                    ->children()
                                                        ->scalarNode('type')->defaultValue('string')->end()
                                                        ->scalarNode('label')->isRequired()->end()
                                                        ->scalarNode('help_text')->defaultNull()->end()
                                                        ->scalarNode('placeholder')->defaultNull()->end()
                                                        ->scalarNode('required')->defaultFalse()->end()
                                                        ->scalarNode('choices_provider')->defaultNull()->end()
                                                        ->arrayNode('choices')
                                                            ->arrayPrototype()
                                                                ->children()
                                                                    ->scalarNode('value')->isRequired()->end()
                                                                    ->scalarNode('label')->defaultValue('')->end()
                                                                ->end()
                                                            ->end()
                                                        ->end()
                                                    ->end()
                                                ->end()
                                            ->end()
                                            ->append($this->buildConstraintsNode('custom_constraints'))
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('content_type')
                            ->arrayPrototype()
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('controller')->defaultValue('')->end()
                                    ->scalarNode('layout_builder')->isRequired()->end()
                                    ->booleanNode('multilingual')->defaultTrue()->end()
                                    ->booleanNode('configurable')->defaultTrue()->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('content_type_entry')
                            ->arrayPrototype()
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('type')->isRequired()->end()
                                    ->scalarNode('name')->isRequired()->end()
                                    ->scalarNode('icon')->defaultNull()->end()
                                    ->scalarNode('controller')->defaultNull()->end()
                                    ->booleanNode('is_routable')->defaultFalse()->end()
                                    ->booleanNode('is_hierarchical')->defaultFalse()->end()
                                    ->scalarNode('routing_strategy')->defaultNull()->end()
                                    ->arrayNode('layout')
                                        ->children()
                                            ->arrayNode('sections')
                                                ->arrayPrototype()
                                                ->children()
                                                    ->arrayNode('groups')
                                                        ->arrayPrototype()
                                                            ->children()
                                                                ->scalarNode('name')->isRequired()->end()
                                                                ->booleanNode('active')->defaultFalse()->end()
                                                                ->integerNode('order')->defaultValue(1)->end()
                                                                ->append($this->buildContentTypeFieldsNode('fields'))
                                                            ->end()
                                                        ->end()
                                                    ->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function registerContentBlockConfiguration(ArrayNodeDefinition $root): void
    {
        $root
            ->children()
                ->arrayNode('content_blocks')
                    ->children()
                        ->arrayNode('templating')
                            ->children()
                                ->arrayNode('paths')->scalarPrototype()->defaultValue([])->end()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function registerWidgetsConfiguration(ArrayNodeDefinition $root): void
    {
        $root
            ->children()
                ->arrayNode('widgets')
                    ->arrayPrototype()
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('classname')->isRequired()->end()
                            ->scalarNode('name')->isRequired()->end()
                            ->scalarNode('views')->isRequired()->end()
                            ->scalarNode('description')->defaultNull()->end()
                            ->scalarNode('translation_domain')->defaultNull()->end()
                            ->append($this->buildContentTypeFieldsNode('fields'))
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function registerFilemanagerConfiguration(ArrayNodeDefinition $root): void
    {
        $root
            ->children()
                ->arrayNode('filemanager')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('image_sizes')
                            ->arrayPrototype()
                                ->children()
                                    ->scalarNode('name')->isRequired()->end()
                                    ->integerNode('width')->defaultNull()->end()
                                    ->integerNode('height')->defaultNull()->end()
                                    ->scalarNode('mode')->defaultValue('fit')->end()
                                    ->scalarNode('translation_domain')->defaultNull()->end()
                                ->end()
                            ->end()
                            ->validate()->always($this->validateCollectionsNames(...))->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function registerImporterConfiguration(ArrayNodeDefinition $root): void
    {
        $root
            ->children()
                ->arrayNode('importer')
                    ->children()
                        ->arrayNode('objects')
                            ->arrayPrototype()
                                ->children()
                                    ->scalarNode('importer')->defaultNull()->end()
                                    ->arrayNode('mapping')
                                        ->arrayPrototype()
                                            ->children()
                                                ->scalarNode('type')->defaultValue('string')->end()
                                                ->booleanNode('required')->defaultFalse()->end()
                                                ->variableNode('default_value')->defaultNull()->end()
                                                ->scalarNode('collection_of')->defaultNull()->end()
                                            ->end()
                                        ->end()
                                        ->validate()
                                            ->always(function (array $fields) {
                                                foreach ($fields as $key => $field) {
                                                    if ($fields[$key]['collection_of']) {
                                                        $fields[$key]['type'] = $fields[$key]['collection_of'];
                                                        $fields[$key]['collection'] = true;
                                                        $fields[$key]['default_value'] = [];
                                                    } else {
                                                        $fields[$key]['collection'] = false;
                                                    }

                                                    if ($field['type'] === 'one_dimension_array') {
                                                        $fields[$key]['default_value'] = [];
                                                    }

                                                    unset($fields[$key]['collection_of']);
                                                }

                                                return $fields;
                                            })
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function buildConstraintsNode(string $nodeName): NodeDefinition
    {
        $treeBuilder = new TreeBuilder($nodeName);

        return $treeBuilder->getRootNode()
            ->arrayPrototype()
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('classname')->isRequired()->end()
                    ->scalarNode('label')->isRequired()->end()
                    ->scalarNode('help_text')->defaultNull()->end()
                    ->arrayNode('modificators')
                        ->arrayPrototype()
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('type')->isRequired()->end()
                                ->scalarNode('label')->isRequired()->end()
                                ->scalarNode('value')->defaultNull()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function buildContentTypeFieldsNode(string $nodeName): NodeDefinition
    {
        $treeBuilder = new TreeBuilder($nodeName);

        return $treeBuilder->getRootNode()
            ->arrayPrototype()
                ->children()
                    ->scalarNode('type')->isRequired()->end()
                    ->scalarNode('name')->isRequired()->end()
                    ->scalarNode('translation_domain')->defaultValue('content_builder.field')->end()
                    ->booleanNode('is_multilingual')->defaultFalse()->end()
                    ->scalarNode('parent')->defaultNull()->end()
                    ->arrayNode('configuration')
                        ->arrayPrototype()
                            ->children()
                                ->scalarNode('code')->isRequired()->end()
                                ->scalarNode('value')->isRequired()->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('constraints')
                        ->arrayPrototype()
                            ->children()
                                ->scalarNode('code')->isRequired()->end()
                                ->arrayNode('modificators')
                                    ->arrayPrototype()
                                        ->children()
                                            ->scalarNode('code')->isRequired()->end()
                                            ->scalarNode('value')->isRequired()->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function registerTwigConfiguration(ArrayNodeDefinition $root): void
    {
        $root
            ->children()
                ->arrayNode('twig')
                    ->children()
                        ->arrayNode('loader')
                            ->children()
                                ->arrayNode('array')
                                    ->children()
                                        ->arrayNode('templates')
                                            ->useAttributeAsKey('name')
                                            ->arrayPrototype()
                                                ->addDefaultsIfNotSet()
                                                ->children()
                                                    ->scalarNode('template')->isRequired()->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                                ->arrayNode('filesystem')
                                    ->children()
                                        ->arrayNode('paths')
                                            ->useAttributeAsKey('name')
                                            ->arrayPrototype()
                                                ->addDefaultsIfNotSet()
                                                ->children()
                                                    ->scalarNode('path')->isRequired()->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('layout')
                            ->children()
                                ->arrayNode('themes')
                                    ->scalarPrototype()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function registerTemplatingConfiguration(ArrayNodeDefinition $root): void
    {
        $root
            ->children()
                ->arrayNode('templating')
                    ->children()
                        ->arrayNode('paths')
                            ->arrayPrototype()
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('name')->isRequired()->end()
                                    ->scalarNode('path')->isRequired()->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function registerAssetsConfiguration(ArrayNodeDefinition $root): void
    {
        $root
            ->children()
                ->arrayNode('public_paths')
                    ->arrayPrototype()
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('source')->end()
                            ->scalarNode('target')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function registerAssetterConfiguration(ArrayNodeDefinition $root): void
    {
        $root
            ->children()
                ->arrayNode('assetter')
                    ->fixXmlConfig('asset')
                    ->children()
                        ->arrayNode('assets')
                            ->useAttributeAsKey('name')
                            ->arrayPrototype()
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->arrayNode('scripts')
                                        ->scalarPrototype()->defaultValue([])->end()
                                    ->end()
                                    ->arrayNode('styles')
                                        ->scalarPrototype()->defaultValue([])->end()
                                    ->end()
                                    ->arrayNode('require')
                                        ->scalarPrototype()->defaultValue([])->end()
                                    ->end()
                                    ->scalarNode('collection')->defaultNull()->end()
                                    ->scalarNode('group')->defaultValue('body')->end()
                                    ->scalarNode('priority')->defaultValue('100')->end()
                                    ->arrayNode('included')
                                        ->scalarPrototype()->defaultValue([])->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function registerThemeConfiguration(ArrayNodeDefinition $root): void
    {
        $root
            ->children()
                ->arrayNode('theme')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('changeset')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('base_class')->defaultValue(Changeset::class)->end()
                            ->end()
                        ->end()
                        ->arrayNode('configuration')
                            ->arrayPrototype()
                                ->children()
                                    ->arrayNode('imports')
                                        ->children()
                                            ->arrayNode('collections')
                                                ->arrayPrototype()
                                                    ->children()
                                                        ->scalarNode('name')->isRequired()->end()
                                                        ->scalarNode('filepath')->isRequired()->end()
                                                    ->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                    ->scalarNode('translation_domain')->isRequired()->end()
                                    ->scalarNode('css_framework')->isRequired()->end()
                                    ->arrayNode('configuration')
                                        ->children()
                                            ->arrayNode('base')
                                                ->children()
                                                    ->arrayNode('assets')->scalarPrototype()->defaultValue([])->end()->end()
                                                    ->scalarNode('node_content_field')->defaultValue('content')->end()
                                                    ->arrayNode('widget_spaces')
                                                        ->arrayPrototype()
                                                            ->children()
                                                                ->scalarNode('name')->isRequired()->end()
                                                                ->scalarNode('label')->isRequired()->end()
                                                            ->end()
                                                        ->end()
                                                        ->validate()->always($this->validateCollectionsNames(...))->end()
                                                    ->end()
                                                    ->arrayNode('widget_styles')
                                                        ->arrayPrototype()
                                                            ->children()
                                                                ->scalarNode('name')->isRequired()->end()
                                                                ->scalarNode('label')->isRequired()->end()
                                                            ->end()
                                                        ->end()
                                                        ->validate()->always($this->validateCollectionsNames(...))->end()
                                                    ->end()
                                                    ->arrayNode('image_sizes')
                                                        ->arrayPrototype()
                                                            ->children()
                                                                ->scalarNode('name')->isRequired()->end()
                                                                ->integerNode('width')->defaultNull()->end()
                                                                ->integerNode('height')->defaultNull()->end()
                                                                ->scalarNode('mode')->defaultValue('fit')->end()
                                                            ->end()
                                                        ->end()
                                                        ->validate()->always($this->validateCollectionsNames(...))->end()
                                                    ->end()
                                                ->end()
                                            ->end()
                                            ->arrayNode('customizer')
                                                ->children()
                                                    ->arrayNode('assets')->scalarPrototype()->defaultValue([])->end()->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                    ->arrayNode('customizer')
                                        ->children()
                                            ->variableNode('variables')
                                                ->defaultValue([])
                                                ->validate()
                                                ->always(function (array $variables) {
                                                    foreach ($variables as $key => $val) {
                                                        if (!is_array($val)) {
                                                            throw new \InvalidArgumentException(sprintf('Value of cms.theme.configuration.[theme].customizer.variables.%s must be an array.', $key));
                                                        }

                                                        foreach ($val as $variable => $value) {
                                                            if (!is_string($variable)) {
                                                                throw new \InvalidArgumentException(sprintf('Key of cms.theme.configuration.[theme].customizer.variables.%s.%s must be a string.', $key, $variable));
                                                            }
                                                            if (!is_scalar($value)) {
                                                                throw new \InvalidArgumentException(sprintf('Value of cms.theme.configuration.[theme].customizer.variables.%s.%s must be a scalar.', $key, $variable));
                                                            }
                                                        }
                                                    }

                                                    return $variables;
                                                })
                                                ->end()
                                            ->end()
                                            ->arrayNode('changesets')
                                                ->arrayPrototype()
                                                    ->children()
                                                        ->scalarNode('label')->isRequired()->end()
                                                        ->scalarNode('description')->defaultNull()->end()
                                                        ->variableNode('data')->defaultValue([])->end()
                                                    ->end()
                                                ->end()
                                            ->end()
                                            ->arrayNode('builder')
                                                ->arrayPrototype()
                                                    ->addDefaultsIfNotSet()
                                                    ->children()
                                                        ->scalarNode('label')->defaultNull()->end()
                                                        ->scalarNode('description')->defaultNull()->end()
                                                        ->scalarNode('parent')->defaultNull()->end()
                                                        ->arrayNode('controls')
                                                            ->arrayPrototype()
                                                                ->addDefaultsIfNotSet()
                                                                ->children()
                                                                    ->scalarNode('type')->defaultValue('text')->end()
                                                                    ->scalarNode('label')->isRequired()->end()
                                                                    ->variableNode('control_options')->defaultValue([])->end()
                                                                    ->variableNode('value')->defaultNull()->end()
                                                                    ->scalarNode('transport')->defaultValue('refresh')->end()
                                                                    ->booleanNode('multilingual')->defaultFalse()->end()
                                                                ->end()
                                                            ->end()
                                                        ->end()
                                                    ->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function registerHooksConfiguration(ArrayNodeDefinition $root): void
    {
        $root
            ->children()
                ->arrayNode('hooks')
                    ->children()
                        ->arrayNode('actions')
                            ->useAttributeAsKey('name')
                            ->arrayPrototype()
                                ->children()
                                    ->arrayNode('parameters')
                                        ->arrayPrototype()
                                        ->children()
                                            ->scalarNode('mode')->defaultValue('append')->end()
                                            ->scalarNode('service')->defaultNull()->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function registerSearchAnythingConfiguration(ArrayNodeDefinition $root): void
    {
        $root
            ->children()
                ->arrayNode('search_anything')
                    ->children()
                        ->arrayNode('indexes')
                            ->useAttributeAsKey('name')
                            ->arrayPrototype()
                                ->children()
                                    ->enumNode('localization_strategy')->values(['content', 'user', 'unilingual'])->isRequired()->end()
                                    ->enumNode('multisite_strategy')->values(['website', 'global'])->isRequired()->end()
                                    ->scalarNode('collector')->isRequired()->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function validateCollectionsNames(array $collection): array
    {
        foreach ($collection as $element) {
            if (!preg_match('#^([a-z0-9\-]+)$#i', $element['name'])) {
                throw new \InvalidArgumentException(sprintf('Name "%s" must be named with alphanumeric string with optional dash', $element['name']));
            }
        }

        return $collection;
    }
}
