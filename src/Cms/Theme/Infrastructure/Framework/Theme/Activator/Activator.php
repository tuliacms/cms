<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Infrastructure\Framework\Theme\Activator;

use Tulia\Cms\Options\Domain\WriteModel\OptionsRepositoryInterface;
use Tulia\Component\Theme\Activator\ActivatorInterface;
use Tulia\Component\Theme\Exception\MissingThemeException;
use Tulia\Component\Theme\Storage\StorageInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Activator implements ActivatorInterface
{
    public function __construct(
        private readonly StorageInterface $storage,
        private readonly OptionsRepositoryInterface $options,
    ) {
    }

    public function activate(string $name, string $websiteId): void
    {
        if ($this->storage->has($name) === false) {
            throw new MissingThemeException(sprintf('Theme %s not found in storage.', $name));
        }

        $option = $this->options->get('theme', $websiteId);
        $option->setValue($name);
        $this->options->save($option);
    }
}
