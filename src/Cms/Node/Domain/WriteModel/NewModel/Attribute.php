<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\NewModel;

use Tulia\Cms\Content\Attributes\Domain\WriteModel\Model\Attribute as BaseAttribute;

/**
 * @author Adam Banaszkiewicz
 * @final
 */
class Attribute extends BaseAttribute
{
    private string $id;
    private Node $node;
}
