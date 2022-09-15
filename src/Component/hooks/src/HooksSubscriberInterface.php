<?php

declare(strict_types=1);

namespace Tulia\Component\Hooks;

/**
 * @author Adam Banaszkiewicz
 */
interface HooksSubscriberInterface
{
    public static function getSubscribedActions(): array;
}
