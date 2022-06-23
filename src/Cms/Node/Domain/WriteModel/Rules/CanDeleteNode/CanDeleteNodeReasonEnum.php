<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Rules\CanDeleteNode;

/**
 * @author Adam Banaszkiewicz
 */
enum CanDeleteNodeReasonEnum: string
{
    case NodeHasChildren = 'Node has child subnodes';
    case OK = 'OK';
}
