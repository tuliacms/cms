<?php

declare(strict_types=1);

namespace Tulia\Bundle\CmsBundle;

use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tulia\Bundle\CmsBundle\DependencyInjection\CompilerPass\CachePass;
use Tulia\Bundle\CmsBundle\DependencyInjection\CompilerPass\ContentBuilderPass;
use Tulia\Bundle\CmsBundle\DependencyInjection\CompilerPass\DashboardPass;
use Tulia\Bundle\CmsBundle\DependencyInjection\CompilerPass\FinderPass;
use Tulia\Bundle\CmsBundle\DependencyInjection\CompilerPass\HooksPass;
use Tulia\Bundle\CmsBundle\DependencyInjection\CompilerPass\RoutingPass;
use Tulia\Bundle\CmsBundle\DependencyInjection\CompilerPass\TaxonomyPass;
use Tulia\Bundle\CmsBundle\DependencyInjection\CompilerPass\TemplatingPass;
use Tulia\Bundle\CmsBundle\DependencyInjection\CompilerPass\ThemePass;
use Tulia\Bundle\CmsBundle\DependencyInjection\CompilerPass\UsecasePass;
use Tulia\Bundle\CmsBundle\DependencyInjection\CompilerPass\WidgetPass;
use Tulia\Bundle\CmsBundle\DependencyInjection\TuliaCmsExtension;
use Tulia\Cms\Security\Framework\DependencyInjection\CompilerPass\SecurityPass;
use Tulia\Component\Importer\Implementation\Symfony\DependencyInjection\CompilerPass\ImporterPass;

/**
 * @author Adam Banaszkiewicz
 */
class TuliaCmsBundle extends Bundle
{
    public function getContainerExtension(): ExtensionInterface
    {
        return new TuliaCmsExtension();
    }

    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new CachePass());
        $container->addCompilerPass(new TaxonomyPass());
        $container->addCompilerPass(new DashboardPass());
        $container->addCompilerPass(new WidgetPass());
        $container->addCompilerPass(new ContentBuilderPass());
        $container->addCompilerPass(new ImporterPass());
        $container->addCompilerPass(new TemplatingPass());
        $container->addCompilerPass(new RoutingPass());
        $container->addCompilerPass(new SecurityPass());
        $container->addCompilerPass(new FinderPass());
        $container->addCompilerPass(new ThemePass());
        $container->addCompilerPass(new UsecasePass());
        $container->addCompilerPass(new HooksPass());

        /**
         * Parameter should be used in all Core configs, instead of "kernel.project_dir".
         * It points to the /src directory of the core system.
         */
        $container->setParameter('cms.core_dir', dirname(__DIR__, 2));

        $container->addResource(new FileResource($container->getParameter('kernel.project_dir').'/config/dynamic.php'));
    }
}
