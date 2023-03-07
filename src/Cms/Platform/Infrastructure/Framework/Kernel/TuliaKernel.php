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

    private array $extensions = [];

    public function getProjectDir(): string
    {
        return __TULIA_PROJECT_DIR;
    }

    public function boot(): void
    {
        parent::boot();

        date_default_timezone_set($this->getContainer()->getParameter('timezone'));
    }

    public function getPublicDir(): string
    {
        return $this->getProjectDir() . '/public';
    }

    public function registerBundles(): iterable
    {
        $contents = require $this->getConfigDir() . '/bundles.php';

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
            $this->getProjectDir() . '/config',
            $base . '/Activity/Framework/Resources/config',
            $base . '/BackendMenu/Framework/Resources/config',
            $base . '/BodyClass/Framework/Resources/config',
            $base . '/Breadcrumbs/Framework/Resources/config',
            $base . '/ContactForm/Infrastructure/Framework/Resources/config',
            $base . '/EditLinks/Framework/Resources/config',
            $base . '/Filemanager/Infrastructure/Framework/Resources/config',
            $base . '/FrontendToolbar/Framework/Resources/config',
            $base . '/Homepage/Infrastructure/Framework/Resources/config',
            $base . '/Menu/Infrastructure/Framework/Resources/config',
            $base . '/Node/Infrastructure/Framework/Resources/config',
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
            $base . '/Seo/Infrastructure/Framework/Resources/config',
            $base . '/Extension/Infrastructure/Framework/Resources/config',
        ];

        if ($this->environment === 'dev') {
            $dirs[] = $base . '/Deployment/Infrastructure/Framework/Resources/config';
        }

        $dirs = array_merge($dirs, $this->getComposerExtensionConfigDirs());

        return $dirs;
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $this->resolveExtensions();

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
        $container->parameters()->set('cms.extensions.themes', $this->extensions['themes']);
        $container->parameters()->set('cms.extensions.modules', $this->extensions['modules']);

        $root = $this->getProjectDir();
        foreach ($this->extensions as $type => $packages) {
            foreach ($packages as $name => $info) {
                $container->parameters()->set($name, $root . $info['path']);
            }
        }
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

    private function resolveExtensions(): void
    {
        $root = $this->getProjectDir();
        $extensionsSource = json_decode(file_get_contents($root.'/composer.extensions.json'), true, JSON_THROW_ON_ERROR);
        $this->extensions = [
            'themes' => $extensionsSource['extra']['tuliacms']['themes'] ?? [],
            'modules' => $extensionsSource['extra']['tuliacms']['modules'] ?? [],
        ];
    }

    private function getComposerExtensionConfigDirs(): array
    {
        $root = $this->getProjectDir();
        $result = [];

        foreach ($this->extensions['themes'] ?? [] as $code => $info) {
            if (!is_dir($root.$info['path'].'/Resources/config')) {
                throw new \RuntimeException(sprintf(
                    'The "%s" directory of theme "%s" does not exists.',
                    $info['path'].'/Resources/config',
                    $code,
                ));
            }

            $result[] = $root.$info['path'].'/Resources/config';
        }
        foreach ($this->extensions['modules'] ?? [] as $code => $info) {
            if (!is_dir($root.$info['path'].'/Resources/config')) {
                throw new \RuntimeException(sprintf(
                    'The "%s" directory of module "%s" does not exists.',
                    $info['path'].'/Resources/config',
                    $code,
                ));
            }

            $result[] = $root.$info['path'].'/Resources/config';
        }

        return $result;
    }
}
