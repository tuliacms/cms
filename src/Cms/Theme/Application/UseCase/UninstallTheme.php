<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Application\UseCase;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelEvents;
use Tulia\Cms\Platform\Application\Service\FrameworkCacheService;
use Tulia\Cms\Platform\Infrastructure\Composer\Extensions\ExtensionsStorage;
use Tulia\Cms\Shared\Application\UseCase\AbstractTransactionalUseCase;
use Tulia\Cms\Shared\Application\UseCase\RequestInterface;
use Tulia\Cms\Shared\Application\UseCase\ResultInterface;
use Tulia\Component\Theme\Storage\StorageInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class UninstallTheme extends AbstractTransactionalUseCase
{
    public function __construct(
        private readonly string $projectDir,
        private readonly FrameworkCacheService $frameworkCacheService,
        private readonly StorageInterface $storage,
        private readonly ExtensionsStorage $extensionsStorage,
        private readonly EventDispatcherInterface $dispatcher,
    ) {
    }

    /**
     * @param RequestInterface&UninstallThemeRequest $request
     */
    protected function execute(RequestInterface $request): ?ResultInterface
    {
        $this->denyIfNotDevelopmentEnvironment();

        $themeDirectory = $this->projectDir.'/extension/theme/'.$request->theme;

        if (!is_dir($themeDirectory)) {
            throw new \Exception('Theme does not exists');
        }

        $theme = $this->storage->get($request->theme);

        if ($theme->isLocal()) {
            $this->extensionsStorage->removeTheme($request->theme);

            $this->dispatcher->addListener(KernelEvents::TERMINATE, function () use ($themeDirectory) {
                $filesystem = new Filesystem();
                $filesystem->remove($themeDirectory);

                $this->frameworkCacheService->clear();
            });
        }

        return null;
    }
}
