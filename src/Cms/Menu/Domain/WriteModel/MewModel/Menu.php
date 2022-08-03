<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\WriteModel\MewModel;

use Tulia\Cms\Menu\Domain\WriteModel\Event\MenuCreated;
use Tulia\Cms\Shared\Domain\WriteModel\Model\AbstractAggregateRoot;

/**
 * @author Adam Banaszkiewicz
 * @final
 */
class Menu extends AbstractAggregateRoot
{
    private function __construct(
        private string $id,
        private string $name
    ) {
        $this->recordThat(new MenuCreated($this->id));
    }

    public static function create(
        string $id,
        string $name
    ) : self {
        return new self($id, $name);
    }


}
