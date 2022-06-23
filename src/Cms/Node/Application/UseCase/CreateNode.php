<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Application\UseCase;

use Tulia\Cms\Content\Attributes\Domain\WriteModel\Model\Attribute;

/**
 * @author Adam Banaszkiewicz
 */
class CreateNode extends AbstractNodeUseCase
{
    /**
     * @param Attribute[] $attributes
     */
    public function __invoke(string $nodeType, string $author, array $details, array $attributes): void
    {
        $node = $this->repository->createNew($nodeType, $author);

        $this->updateModel($node, $details, $attributes);
        $this->create($node);
    }
}
