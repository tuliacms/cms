<?php

declare(strict_types=1);

namespace Tulia\Bundle\CmsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author Adam Banaszkiewicz
 */
class TuliaCmsExtension extends Extension
{
    public function getAlias(): string
    {
        return 'cms';
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $paths = $config['content_blocks']['templating']['paths'];

        foreach ($paths as $key => $path) {
            $paths[$key] = rtrim($path, '/') . '/';
        }

        $projectDir = $container->getParameter('kernel.project_dir');

        $container->setParameter('cms.content_blocks.templating.paths', $paths);
        $container->setParameter('cms.content_builder.content_type_entry.config', $config['content_building']['content_type_entry']);
        $container->setParameter('cms.content_builder.content_type.config', $config['content_building']['content_type']);
        $container->setParameter('cms.content_builder.data_types.mapping', $config['content_building']['data_types']['mapping']);
        $container->setParameter('cms.content_builder.constraint_types.mapping', $config['content_building']['constraint_types']['mapping']);
        $container->setParameter('cms.options.definitions', $this->validateOptionsValues($config['options']['definitions'] ?? []));
        $container->setParameter('cms.widgets', $config['widgets'] ?? []);
        $container->setParameter('cms.filemanager.image_sizes', $config['filemanager']['image_sizes'] ?? []);
        $container->setParameter('cms.importer.objects', $config['importer']['objects'] ?? []);

        $container->setParameter('cms.assetter.assets', $config['assetter']['assets'] ?? []);
        $container->setParameter('cms.assets.public_paths', $this->prepareAssetsPublicPaths($config['public_paths'] ?? [], $projectDir));
        $container->setParameter('cms.twig.loader.array.templates', $this->prepareTwigArrayLoaderTemplates($config['twig']['loader']['array']['templates'] ?? []));
        $container->setParameter('cms.templating.paths', $this->prepareTemplatingPaths($config['templating']['paths'] ?? []));
        $container->setParameter('cms.themes.configuration', $config['theme']['configuration'] ?? []);
        $container->setParameter('cms.hooks.actions', $config['hooks']['actions'] ?? []);
        $container->setParameter('cms.search_anything.indexes', $config['search_anything']['indexes'] ?? []);

        // Finders
        $container->registerForAutoconfiguration(\Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\AbstractFinder::class)
            ->addTag('finder');
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
        $container->registerForAutoconfiguration(\Tulia\Component\Importer\ObjectImporter\Decorator\ObjectImporterDecoratorInterface::class)
            ->addTag('importer.object_importer.decorator');
        // Usecases
        $container->registerForAutoconfiguration(\Tulia\Cms\Shared\Application\UseCase\TransactionalUseCaseInterface::class)
            ->addTag('usecase.transactional');

        // BodyClass
        $container->registerForAutoconfiguration(\Tulia\Cms\BodyClass\Collector\BodyClassCollectorInterface::class)
            ->addTag('body_class.collector');

        // Breadcrumbs
        $container->registerForAutoconfiguration(\Tulia\Cms\Breadcrumbs\Domain\BreadcrumbsResolverInterface::class)
            ->addTag('breadcrumbs.resolver');

        // Dashboard
        $container->registerForAutoconfiguration(\Tulia\Cms\Homepage\UserInterface\Web\Backend\Tiles\DashboardTilesCollector::class)
            ->addTag('dashboard.tiles_collector');
        $container->registerForAutoconfiguration(\Tulia\Cms\Homepage\UserInterface\Web\Backend\Widgets\DashboardWidgetInterface::class)
            ->addTag('dashboard.widget');
        $container->registerForAutoconfiguration(\Tulia\Cms\BackendMenu\Builder\BuilderInterface::class)
            ->addTag('backend_menu.builder');

        // EditLinks
        $container->registerForAutoconfiguration(\Tulia\Cms\EditLinks\Service\EditLinksCollectorInterface::class)
            ->addTag('edit_links.collector');

        // FrontendToolbar
        $container->registerForAutoconfiguration(\Tulia\Cms\FrontendToolbar\Links\LinksCollectorInterface::class)
            ->addTag('frontend_toolbar.links.provider');

        // Menus
        $container->registerForAutoconfiguration(\Tulia\Cms\Menu\Domain\Builder\Type\RegistratorInterface::class)
            ->addTag('menu.builder.type_registrator');

        // Nodes
        $container->registerForAutoconfiguration(\Tulia\Cms\Node\Domain\WriteModel\Service\NodePurpose\NodePurposeProviderInterface::class)
            ->addTag('node.purpose_provider');

        // Terms
        $container->registerForAutoconfiguration(\Tulia\Cms\Taxonomy\Domain\WriteModel\ActionsChain\TaxonomyActionInterface::class)
            ->addTag('term.action_chain');

        // ContentBuilder
        $container->registerForAutoconfiguration(\Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeDecoratorInterface::class)
            ->addTag('content_builder.content_type.decorator');
        $container->registerForAutoconfiguration(\Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeProviderInterface::class)
            ->addTag('content_builder.content_type.provider');
        $container->registerForAutoconfiguration(\Tulia\Cms\Content\Type\Domain\WriteModel\Routing\Strategy\ContentTypeRoutingStrategyInterface::class)
            ->addTag('content_builder.content_type.routing_strategy');
        $container->registerForAutoconfiguration(\Tulia\Cms\Content\Type\Infrastructure\Framework\Form\Service\LayoutTypeBuilderInterface::class)
            ->addTag('content_builder.layout_type.builder');
        $container->registerForAutoconfiguration(\Tulia\Cms\Content\Type\Domain\ReadModel\FieldTypeBuilder\FieldTypeBuilderInterface::class)
            ->addTag('content_builder.data_types.builder')
            ->setShared(false)
            ->setLazy(true);
        $container->registerForAutoconfiguration(\Tulia\Cms\Content\Type\Domain\ReadModel\FieldTypeHandler\FieldTypeHandlerInterface::class)
            ->addTag('content_builder.data_types.handler')
            ->setShared(false)
            ->setLazy(true);
        $container->registerForAutoconfiguration(\Tulia\Cms\Content\Type\Domain\ReadModel\FieldChoicesProvider\FieldChoicesProviderInterface::class)
            ->addTag('content_builder.data_types.configuration.choices_provider')
            ->setShared(false)
            ->setLazy(true);

        // Shortcode
        $container->registerForAutoconfiguration(\Tulia\Component\Shortcode\Compiler\ShortcodeCompilerInterface::class)
            ->addTag('shortcode.compiler');

        // Widgets
        $container->registerForAutoconfiguration(\Tulia\Cms\Widget\Domain\Catalog\WidgetInterface::class)
            ->setLazy(true)
            ->addTag('cms.widget');

        // Filemanager
        $container->registerForAutoconfiguration(\Tulia\Cms\Filemanager\Domain\ImageSize\ImagesSizeProviderInterface::class)
            ->setLazy(true)
            ->addTag('filemanager.image_size.provider');

        // SearchAnything
        $container->registerForAutoconfiguration(\Tulia\Cms\SearchAnything\Domain\WriteModel\Service\DocumentCollectorInterface::class)
            ->setLazy(true)
            ->addTag('search_anything.document_collector');

        // Hooks
        $container->registerForAutoconfiguration(\Tulia\Component\Hooks\HooksSubscriberInterface::class)
            ->setLazy(true)
            ->addTag('hooks.subscriber');

        $container->registerForAutoconfiguration(\Tulia\Cms\WysiwygEditor\Application\WysiwygEditorInterface::class)
            ->addTag('wysiwyg_editor');
        $container->registerForAutoconfiguration(\Tulia\Cms\Deployment\Domain\WriteModel\Service\DeploymentFileGeneratorInterface::class)
            ->addTag('deployment.file_generator');
        $container->registerForAutoconfiguration(\Tulia\Cms\Website\Domain\WriteModel\Service\TranslationCopyMachine\CopyBusInterface::class)
            ->setLazy(true)
            ->addTag('translations.copy_machine.bus');
        $container->registerForAutoconfiguration(\Tulia\Cms\Settings\Domain\Group\SettingsGroupInterface::class)
            ->setLazy(true)
            ->addTag('settings.group');
        $container->registerForAutoconfiguration(\Tulia\Cms\Shared\Domain\WriteModel\Service\SlugGeneratorStrategy\SlugExistenceCheckerInterface::class)
            ->addTag('slugs.generator.existence_checker');
    }

    protected function validateOptionsValues(array $definitions): array
    {
        foreach ($definitions as $name => $definition) {
            if ($definition['type'] === 'array' && \is_array($definition['value']) === false) {
                throw new \InvalidArgumentException(sprintf('Default value of %s option must be an array.', $name));
            }
            if ($definition['type'] === 'boolean' && \is_bool($definition['value']) === false) {
                throw new \InvalidArgumentException(sprintf('Default value of %s option must be a boolean.', $name));
            }
            if ($definition['type'] === 'number' && \is_numeric($definition['value']) === false) {
                throw new \InvalidArgumentException(sprintf('Default value of %s option must be numeric.', $name));
            }
        }

        return $definitions;
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

    private function prepareAssetsPublicPaths(array $paths, string $projectDir): array
    {
        foreach ($paths as $key => $path) {
            if ($path['source'][0] !== '/') {
                $paths[$key]['source'] = $projectDir.'/'.$path['source'];
            }
        }

        return $paths;
    }
}
