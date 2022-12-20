<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Application\UseCase;

use Tulia\Cms\Node\Domain\WriteModel\Model\Attribute;
use Tulia\Cms\Node\Domain\WriteModel\Model\Node;
use Tulia\Cms\Node\Domain\WriteModel\Rules\CanAddPurpose\CanImposePurposeInterface;
use Tulia\Cms\Node\Domain\WriteModel\Service\NodeRepositoryInterface;
use Tulia\Cms\Node\Domain\WriteModel\Service\ShortcodeProcessorInterface;
use Tulia\Cms\Shared\Application\UseCase\AbstractTransactionalUseCase;
use Tulia\Cms\Shared\Domain\WriteModel\Model\ValueObject\ImmutableDateTime;
use Tulia\Cms\Shared\Domain\WriteModel\Service\SlugGeneratorStrategy\SlugGeneratorStrategyInterface;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractNodeUseCase extends AbstractTransactionalUseCase
{
    public function __construct(
        protected readonly NodeRepositoryInterface $repository,
        protected readonly EventBusInterface $eventBus,
        protected readonly CanImposePurposeInterface $canImposePurpose,
        protected readonly SlugGeneratorStrategyInterface $slugGeneratorStrategy,
        protected readonly ShortcodeProcessorInterface $processor,
    ) {
    }

    protected function updateModel(
        CreateNodeRequest|UpdateNodeRequest $request,
        Node $node,
    ): void {
        $details = $request->details;
        $attributes = $request->attributes;

        $node->setStatus($details['status']);
        $node->persistPurposes($this->canImposePurpose, ...$details['purposes']);
        $node->publishNodeAt(new ImmutableDateTime($details['published_at']));

        /*if ($details['parent_id']) {
            $node->moveAsChildOf($this->repository->referenceTo($details['parent_id']));
        } else {*/
        $node->moveAsRootNode();
        //}

        if ($details['published_to']) {
            $node->publishNodeTo(new ImmutableDateTime($details['published_to']));
        } else {
            $node->publishNodeForever();
        }

        $node->persistAttributes($request->locale, $request->defaultLocale, $this->processAttributes($attributes));
        $node->changeTitle($request->locale, $request->defaultLocale, $this->slugGeneratorStrategy, $details['title'], $details['slug']);
    }

    /**
     * @param Attribute[] $attributes
     * @return Attribute[]
     */
    private function processAttributes(array $attributes): array
    {
        foreach ($attributes as $key => $attribute) {
            if (!$attribute->getValue()) {
                continue;
            }

            if ($attribute->isCompilable() === false) {
                continue;
            }

            $attributes[$key] = $attribute->withCompiledValue($this->processor->process((string) $attribute));
        }

        return $attributes;
    }
}
