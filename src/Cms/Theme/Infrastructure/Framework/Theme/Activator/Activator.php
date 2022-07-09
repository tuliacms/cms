<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Infrastructure\Framework\Theme\Activator;

use Tulia\Cms\Platform\Domain\Service\DynamicConfigurationInterface;
use Tulia\Component\Theme\Activator\ActivatorInterface;
use Tulia\Component\Theme\Exception\MissingThemeException;
use Tulia\Component\Theme\Storage\StorageInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Activator implements ActivatorInterface
{
    public function __construct(
        private StorageInterface $storage,
        private DynamicConfigurationInterface $configuration
    ) {
    }

    public function activate(string $name): void
    {
        if ($this->storage->has($name) === false) {
            throw new MissingThemeException(sprintf('Theme %s not found in storage.', $name));
        }

        $this->configuration->open();
        $this->configuration->set('cms.theme', $name);
        $this->configuration->write();
    }
}
