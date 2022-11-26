<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Application\UseCase;

use Symfony\Component\Filesystem\Filesystem;
use Tulia\Cms\Platform\Application\Service\AssetsPublisher;
use Tulia\Cms\Platform\Application\Service\FrameworkCacheService;
use Tulia\Cms\Platform\Infrastructure\Composer\Extensions\ExtensionSourceEnum;
use Tulia\Cms\Platform\Infrastructure\Composer\Extensions\ExtensionsStorage;
use Tulia\Cms\Shared\Application\UseCase\AbstractTransactionalUseCase;
use Tulia\Cms\Shared\Application\UseCase\IdResult;
use Tulia\Cms\Shared\Application\UseCase\RequestInterface;
use Tulia\Cms\Shared\Application\UseCase\ResultInterface;
use ZipArchive;

/**
 * @author Adam Banaszkiewicz
 */
final class InstallTheme extends AbstractTransactionalUseCase
{
    public function __construct(
        private readonly string $projectDir,
        private readonly AssetsPublisher $assetsPublisher,
        private readonly FrameworkCacheService $frameworkCacheService,
        private readonly ExtensionsStorage $extensionsStorage,
    ) {
    }

    /**
     * @param RequestInterface&InstallThemeRequest $request
     */
    protected function execute(RequestInterface $request): ?ResultInterface
    {
        $this->denyIfNotDevelopmentEnvironment();

        $destination = $this->projectDir . '/var/theme-installator/theme-' . date('YmdHisu');

        try {
            $zip = new ZipArchive;
            $res = $zip->open($request->filepath);
            if ($res === true) {
                $zip->extractTo($destination);
                $zip->close();
            } else {
                throw new \Exception('Extraction was not possible.');
            }

            if (!is_file($destination . '/Theme.php')) {
                throw new \Exception(
                    'Cannot find Theme.php file in archive. Probably this is not a valid Theme for Tulia CMS.'
                );
            }

            $theme = file_get_contents($destination . '/Theme.php');

            preg_match('#namespace\sTulia\\\\Theme\\\\([a-z0-9]+)\\\\([a-z0-9]+);#i', $theme, $matches);

            if (count($matches) !== 3) {
                throw new \Exception('Cannot find namespace in Theme file. Maybe the file is not properly written.');
            }

            $themesDirectory = $this->projectDir . '/extension/theme';
            $themeDestination = $themesDirectory . sprintf('/%s/%s', $matches[1], $matches[2]);

            if (is_dir($themeDestination)) {
                throw new \Exception('Such theme is already installed.');
            }

            $filesystem = new Filesystem();
            $filesystem->mkdir($themeDestination);
            $filesystem->mirror($destination, $themeDestination);
            $filesystem->remove($destination);

            $this->frameworkCacheService->clear();

            $this->extensionsStorage->appendTheme(
                sprintf('%s/%s', $matches[1], $matches[2]),
                '0.0.1',
                ExtensionSourceEnum::LOCAL,
                $themeDestination,
            );

            return new IdResult(sprintf('%s/%s', $matches[1], $matches[2]));
        } catch (\Throwable $e) {
            $filesystem = new Filesystem();

            if (is_dir($destination)) {
                $filesystem->remove($destination);
            }

            if (isset($themeDestination) && is_dir($themeDestination)) {
                $filesystem->remove($themeDestination);
            }

            throw $e;
        }
    }
}
