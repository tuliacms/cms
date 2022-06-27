<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Application\UseCase;

use Tulia\Cms\Content\Attributes\Domain\WriteModel\Model\Attribute;
use Tulia\Cms\Node\Domain\WriteModel\Event\NodeUpdated;
use Tulia\Cms\Node\Domain\WriteModel\Model\Node;
use Tulia\Cms\Node\Domain\WriteModel\Model\ValueObject\Author;
use Tulia\Cms\Node\Domain\WriteModel\NodeRepositoryInterface;
use Tulia\Cms\Node\Domain\WriteModel\Rules\CanAddPurpose\CanImposePurposeInterface;
use Tulia\Cms\Shared\Domain\WriteModel\ActionsChain\AggregateActionsChainInterface;
use Tulia\Cms\Shared\Domain\WriteModel\Model\ValueObject\ImmutableDateTime;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractNodeUseCase
{
    public function __construct(
        protected NodeRepositoryInterface $repository,
        protected EventBusInterface $eventBus,
        protected AggregateActionsChainInterface $actionsChain,
        protected CanImposePurposeInterface $canImposePurpose
    ) {
    }

    protected function create(Node $node): void
    {
        $this->actionsChain->execute('create', $node);

        try {
            $this->repository->insert($node);
            $this->eventBus->dispatchCollection($node->collectDomainEvents());
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    protected function update(Node $node): void
    {
        $this->actionsChain->execute('update', $node);

        try {
            $this->repository->update($node);
            $this->eventBus->dispatchCollection($node->collectDomainEvents());
            $this->eventBus->dispatch(NodeUpdated::fromNode($node));
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    /**
     * @param Attribute[] $attributes
     */
    protected function updateModel(Node $node, array $details, array $attributes): void
    {
        $node->setStatus($details['status']);
        $node->setSlug($details['slug']);
        $node->setTitle($details['title']);
        $node->setPublishedAt(new ImmutableDateTime($details['published_at']));
        $node->setParentId($details['parent_id']);
        $node->setAuthor(new Author($details['author_id']));
        $node->persistPurposes($this->canImposePurpose, $details['purposes']);
        $node->updateAttributes($attributes);

        if ($details['published_to']) {
            $node->setPublishedTo(new ImmutableDateTime($details['published_to']));
        } else {
            $node->setPublishedToForever();
        }
    }
}
