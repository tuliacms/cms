<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\ContentType\Domain\WriteModel\Model;

use Tulia\Cms\ContentBuilder\ContentType\Domain\AbstractModel\AbstractField;

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
