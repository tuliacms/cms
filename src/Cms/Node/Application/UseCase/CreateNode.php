<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Application\UseCase;

use Tulia\Cms\Shared\Application\UseCase\RequestInterface;
use Tulia\Cms\Shared\Application\UseCase\ResultInterface;

/**
 * @author Adam Banaszkiewicz
 */
class CreateNode extends AbstractNodeUseCase
{
    /**
     * @param RequestInterface&CreateNodeRequest $request
     */
    protected function execute(RequestInterface $request): ?ResultInterface
    {
        $node = $this->repository->createNew($request->nodeType, $request->author, $request->locale);

        $this->updateModel($node, $request->details, $request->attributes);
        $this->create($node);

        return null;
    }
}
