<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\ReadModel\Model;

use Tulia\Cms\Content\Type\Domain\AbstractModel\AbstractField;

/**
 * @author Adam Banaszkiewicz
 */
class Field extends AbstractField
{
    /**
     * @return Field[]
     */
    public function getChildren(): array
    {
        return parent::getChildren();
    }
}
