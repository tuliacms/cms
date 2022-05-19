<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Domain\WriteModel\ActionsChain;

use Tulia\Cms\Shared\Domain\WriteModel\Model\AbstractAggregateRoot;

/**
 * @author Adam Banaszkiewicz
 */
interface AggregateActionsChainInterface
{
    public function execute(string $name, AbstractAggregateRoot $aggregate): void;

    public function addAction(AggregateActionInterface $action, string $name, string $supportedClass, int $priority): void;
}
