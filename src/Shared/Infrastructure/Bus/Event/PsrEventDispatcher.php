<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Infrastructure\Bus\Event;

use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * @author Adam Banaszkiewicz
 */
class PsrEventDispatcher implements EventBusInterface, EventDispatcherInterface
{
    private EventDispatcherInterface $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function dispatchCollection(array $events): void
    {
        foreach ($events as $event) {
            $this->dispatch($event);
        }
    }

    public function dispatch(object $event): void
    {
        $this->dispatcher->dispatch($event);
    }
}
