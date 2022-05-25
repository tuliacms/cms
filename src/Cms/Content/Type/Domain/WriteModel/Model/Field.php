<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\WriteModel\Model;

use Tulia\Cms\Content\Type\Domain\AbstractModel\AbstractField;

/**
 * @author Adam Banaszkiewicz
 */
final class Field extends AbstractField
{
    /**
     * @return Field[]
     */
    public function getChildren(): array
    {
        return parent::getChildren();
    }
}
