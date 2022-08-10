<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Domain\WriteModel;

use Tulia\Cms\Widget\Domain\WriteModel\Model\Widget;

/**
 * @author Adam Banaszkiewicz
 */
interface WidgetRepositoryInterface
{
    public function getNextId(): string;

    public function get(string $id): Widget;

    public function save(Widget $widget);

    public function delete(Widget $widget): void;
}
