<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Application\UseCase;

use Tulia\Cms\Content\Attributes\Domain\WriteModel\Model\Attribute;
use Tulia\Cms\Node\Domain\WriteModel\Model\Node;

/**
 * @author Adam Banaszkiewicz
 */
class UpdateNode extends AbstractNodeUseCase
{
    /**
     * @param Attribute[] $attributes
     */
    public function __invoke(Node $node, array $attributes): void
    {
        $this->updateModel($node, $attributes);
        $this->update($node);
    }
}
