<?php

declare(strict_types=1);

namespace Tulia\Cms\Settings\Domain\Group;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractSettingsGroupFactory implements SettingsGroupFactoryInterface
{
    abstract public function factory(): iterable;

    public function doFactory(): iterable
    {
        foreach($this->factory() as $group) {
            yield $group;
        }
    }
}
