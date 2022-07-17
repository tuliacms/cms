<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Application\UseCase;

use Tulia\Cms\Node\Domain\WriteModel\NewModel\Node;
use Tulia\Cms\Node\Domain\WriteModel\Rules\CanAddPurpose\CanImposePurposeInterface;
use Tulia\Cms\Node\Domain\WriteModel\Service\NodeRepositoryInterface;
use Tulia\Cms\Node\Domain\WriteModel\Service\ShortcodeProcessorInterface;
use Tulia\Cms\Node\Domain\WriteModel\Service\SlugGeneratorStrategy\SlugGeneratorStrategyInterface;
use Tulia\Cms\Shared\Application\UseCase\AbstractUseTransactionalCase;
use Tulia\Cms\Shared\Domain\WriteModel\Model\ValueObject\ImmutableDateTime;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractNodeUseCase extends AbstractUseTransactionalCase
{
    public function __construct(
        protected NodeRepositoryInterface $repository,
        protected EventBusInterface $eventBus,
        protected CanImposePurposeInterface $canImposePurpose,
        protected SlugGeneratorStrategyInterface $slugGeneratorStrategy,
        protected ShortcodeProcessorInterface $processor
    ) {
    }

    protected function updateModel(
        CreateNodeRequest|UpdateNodeRequest $request,
        Node $node,
    ): void {
        $details = $request->details;
        $attributes = $request->attributes;

        $this->updateCoreModel($node, $details);
        $this->updateTranslationModel($node, $request->locale, $details, $attributes);
    }

    private function updateCoreModel(Node $node, array $details): void
    {
        $node->setStatus($details['status']);
        $node->persistPurposes($this->canImposePurpose, ...$details['purposes']);
        $node->publishNodeAt(new ImmutableDateTime($details['published_at']));

        if ($details['parent_id']) {
            $node->moveAsChildOf($this->repository->referenceTo($details['parent_id']));
        } else {
            $node->moveAsRootNode();
        }

        if ($details['published_to']) {
            $node->publishNodeTo(new ImmutableDateTime($details['published_to']));
        } else {
            $node->publishNodeForever();
        }
    }

    private function updateTranslationModel(Node $node, string $locale, array $details, array $attributes): void
    {
        $translation = $node->translate($locale);
        $translation->persistAttributes(...$this->processAttributes($attributes));
        $translation->rename($this->slugGeneratorStrategy, $details['title'], $details['slug']);
    }

    private function processAttributes(array $attributes): array
    {
        foreach ($attributes as $key => $attribute) {
            if (!$attribute->getValue()) {
                continue;
            }

            if ($attribute->isCompilable() === false) {
                continue;
            }

            $attributes[$key] = $attribute->withCompiledValue($this->processor->process($attribute->getValue()));
        }

        return $attributes;
    }
}
