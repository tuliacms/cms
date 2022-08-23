<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Application\UseCase;

use Tulia\Cms\Shared\Application\UseCase\AbstractTransactionalUseCase;
use Tulia\Cms\Shared\Application\UseCase\RequestInterface;
use Tulia\Cms\Shared\Application\UseCase\ResultInterface;
use Tulia\Cms\Theme\Domain\ThemeImportCollectionRegistry;
use Tulia\Component\Importer\ImporterInterface;
use Tulia\Component\Theme\ManagerInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class ImportThemeCollection extends AbstractTransactionalUseCase
{
    public function __construct(
        private readonly ThemeImportCollectionRegistry $registry,
        private readonly ImporterInterface $importer,
        private readonly ManagerInterface $manager
    ) {
    }

    /**
     * @param RequestInterface&ImportThemeCollectionRequest $request
     */
    protected function execute(RequestInterface $request): ?ResultInterface
    {
        $this->denyIfNotDevelopmentEnvironment();

        $collections = $this->registry->getFor($request->theme);

        if (!isset($collections[$request->collection]['filepath'])) {
            throw new \Exception(sprintf('Collection %s not exists for theme %s.', $request->collection, $request->theme));
        }

        if (!$this->manager->getStorage()->has($request->theme)) {
            throw new \Exception(sprintf('Theme %s does not exists.', $request->theme));
        }

        $theme = $this->manager->getStorage()->get($request->theme);

        $filepath = $theme->getDirectory().'/'.ltrim($collections[$request->collection]['filepath']);

        $this->importer->importFromFile($filepath);

        return null;
    }
}
