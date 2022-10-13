<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Kernel;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

/**
 * @author Adam Banaszkiewicz
 */
final class TuliaKernel extends Kernel
{
    use MicroKernelTrait;

    public function getProjectDir(): string
    {
        return __TULIA_PROJECT_DIR;
    }

    public function getPublicDir(): string
    {
        return $this->getProjectDir() . '/public';
    }

    public function registerBundles(): iterable
    {
        $contents = require dirname(__DIR__) . '/Resources/config/bundles.php';

        foreach ($contents as $class => $envs) {
            if ($envs[$this->environment] ?? $envs['all'] ?? false) {
                yield new $class();
            }
        }
    }

    protected function getConfigDirs(): array
    {
        $base = \dirname(__DIR__, 4);

        $dirs = [
            $base . '/Platform/Infrastructure/Framework/Resources/config',
            $base . '/Activity/Framework/Resources/config',
            $base . '/BackendMenu/Framework/Resources/config',
            $base . '/BodyClass/Framework/Resources/config',
            $base . '/Breadcrumbs/Framework/Resources/config',
            $base . '/ContactForm/Infrastructure/Framework/Resources/config',
            $base . '/EditLinks/Framework/Resources/config',
            $base . '/Filemanager/Infrastructure/Framework/Resources/config',
            $base . '/FrontendToolbar/Framework/Resources/config',
            $base . '/Homepage/Infrastructure/Framework/Resources/config',
            //$base . '/Installator/Infrastructure/Framework/Resources/config',
            $base . '/Menu/Infrastructure/Framework/Resources/config',
            $base . '/Node/Infrastructure/Internal/Framework/Resources/config',
            $base . '/Options/Infrastructure/Framework/Resources/config',
            $base . '/SearchAnything/Infrastructure/Framework/Resources/config',
            $base . '/Security/Framework/Resources/config',
            $base . '/Settings/Infrastructure/Framework/Resources/config',
            $base . '/Taxonomy/Infrastructure/Framework/Resources/config',
            $base . '/Theme/Infrastructure/Framework/Resources/config',
            $base . '/User/Infrastructure/Framework/Resources/config',
            $base . '/Website/Infrastructure/Framework/Resources/config',
            $base . '/Widget/Infrastructure/Framework/Resources/config',
            $base . '/WysiwygEditor/Infrastructure/Framework/Resources/config',
            $base . '/TuliaEditor/Infrastructure/Framework/Resources/config',
            $base . '/Content/Attributes/Infrastructure/Framework/Resources/config',
            $base . '/Content/Block/Infrastructure/Framework/Resources/config',
            $base . '/Content/Type/Infrastructure/Framework/Resources/config',
            $base . '/ImportExport/Infrastructure/Framework/Resources/config',
        ];

        if ($this->environment === 'dev') {
            $dirs[] = $base . '/Deployment/Infrastructure/Framework/Resources/config';
        }

        $dirs = array_merge($dirs, $this->getExtensionConfigDirs('module'));
        $dirs = array_merge($dirs, $this->getExtensionConfigDirs('theme'));

        return $dirs;
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        foreach ($this->getConfigDirs() as $root) {
            if (is_file($root . '/config.yaml')) {
                $container->import($root . '/config.yaml');
            }

            $container->import($root . '/{packages}/*.yaml');
            $container->import($root . '/{packages}/' . $this->environment . '/*.yaml');

            if (is_file($root . '/services.yaml')) {
                $container->import($root . '/services.yaml');
                $container->import($root . '/{services}_' . $this->environment . '.yaml');
            } elseif (is_file($path = $root . '/services.php')) {
                (require $path)($container->withPath($path), $this);
            }
        }

        $class = $this->getContainerClass();
        $buildDir = $this->getBuildDir();
        $cache = new ConfigCache($buildDir.'/'.$class.'.php', $this->debug);

        $container->parameters()->set('kernel.public_dir', $this->getPublicDir());
        $container->parameters()->set('kernel.cache_file', $cache->getPath());
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        foreach ($this->getConfigDirs() as $root) {
            $routes->import($root . '/{routes}/' . $this->environment . '/*.yaml');
            $routes->import($root . '/{routes}/*.yaml');

            if (is_file($root . '/routes.yaml')) {
                $routes->import($root . '/routes.yaml');
            } elseif (is_file($path = $root . '/routes.php')) {
                (require $path)($routes->withPath($path), $this);
            }
        }
    }

    private function getExtensionConfigDirs(string $type): array
    {
        $configDirs = [];

        foreach (['extension', 'extension.local'] as $dir) {
            if (is_dir($this->getProjectDir().'/'.$dir.'/'.$type) === false) {
                continue;
            }

            foreach (new \DirectoryIterator($this->getProjectDir().'/'.$dir.'/'.$type) as $vendor) {
                if ($vendor->isDot() || !$vendor->isDir()) {
                    continue;
                }

                foreach (new \DirectoryIterator($this->getProjectDir().'/'.$dir.'/'.$type.'/'.$vendor->getFilename()) as $ext) {
                    if ($ext->isDot() || !$ext->isDir()) {
                        continue;
                    }

                    $path = $this->getProjectDir().'/'.$dir.'/'.$type.'/'.$vendor->getFilename().'/'.$ext->getFilename().'/Resources/config';

                    if (is_dir($path)) {
                        $configDirs[] = $path;
                    }
                }
            }
        }

        return $configDirs;
    }
}
