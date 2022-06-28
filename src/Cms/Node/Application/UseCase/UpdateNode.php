<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Application\UseCase;

use Tulia\Cms\Shared\Application\UseCase\RequestInterface;
use Tulia\Cms\Shared\Application\UseCase\ResultInterface;

/**
 * @author Adam Banaszkiewicz
 */
class UpdateNode extends AbstractNodeUseCase
{
    protected function execute(RequestInterface|UpdateNodeRequest $request): ?ResultInterface
    {
        $node = $this->repository->find($request->id);

        if (!$node) {
            return null;
        }

        $this->updateModel($node, $request->details, $request->attributes);
        $this->update($node);

        return null;
    }
}
