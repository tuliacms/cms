<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Application\UseCase;

use Symfony\Component\Filesystem\Filesystem;
use Tulia\Cms\Shared\Application\UseCase\AbstractTransactionalUseCase;
use Tulia\Cms\Shared\Application\UseCase\RequestInterface;
use Tulia\Cms\Shared\Application\UseCase\ResultInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class UninstallTheme extends AbstractTransactionalUseCase
{
    public function __construct(
        private readonly string $projectDir
    ) {
    }

    /**
     * @param RequestInterface&UninstallThemeRequest $request
     */
    protected function execute(RequestInterface $request): ?ResultInterface
    {
        $themeDirectory = $this->projectDir.'/extension/theme/'.$request->theme;

        if (!is_dir($themeDirectory)) {
            throw new \Exception('Theme does not exists');
        }

        $filesystem = new Filesystem();
        $filesystem->remove($themeDirectory);

        return null;
    }
}
