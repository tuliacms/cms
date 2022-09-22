<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Application\Service;

use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\Theme\Application\Exception\ThemeNotFoundException;
use Tulia\Cms\Theme\Domain\WriteModel\Event\ThemeActivated;
use Tulia\Component\Theme\Activator\ActivatorInterface;
use Tulia\Component\Theme\Exception\MissingThemeException;
use Tulia\Component\Theme\ManagerInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ThemeActivator
{
    public function __construct(
        private readonly ManagerInterface $manager,
        private readonly ActivatorInterface $activator,
        private readonly EventBusInterface $eventBus,
    ) {
    }

    /**
     * @throws ThemeNotFoundException|MissingThemeException
     */
    public function activateTheme(string $name, string $websiteId): void
    {
        $theme = $this->manager->getStorage()->get($name);

        if (! $theme) {
            throw ThemeNotFoundException::withName($name);
        }

        $this->activator->activate($theme->getName(), $websiteId);

        $this->eventBus->dispatch(new ThemeActivated($name));
    }
}
