<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Application\UseCase;

use Tulia\Cms\Node\Domain\WriteModel\Model\Node;
use Tulia\Cms\Shared\Application\UseCase\IdResult;
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
        $node = Node::create(
            $this->repository->getNextId(),
            $request->nodeType,
            $request->websiteId,
            $request->author,
            $request->data['title'],
            $request->availableLocales,
        );

        $this->updateModel($request, $node);

        $this->repository->save($node);
        $this->eventBus->dispatchCollection($node->collectDomainEvents());

        return new IdResult($node->getId());
    }
}
