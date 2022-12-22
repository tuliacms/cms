<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Application\UseCase;

use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Node\Domain\WriteModel\Model\Attribute;
use Tulia\Cms\Node\Domain\WriteModel\Model\Node;
use Tulia\Cms\Node\Domain\WriteModel\Rules\CanAddPurpose\CanImposePurposeInterface;
use Tulia\Cms\Node\Domain\WriteModel\Service\NodeRepositoryInterface;
use Tulia\Cms\Node\Domain\WriteModel\Service\ParentTermsResolverInterface;
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
        protected readonly ContentTypeRegistryInterface $contentTypeRegistry,
        protected readonly ParentTermsResolverInterface $parentTermsResolver,
    ) {
    }

    protected function updateModel(
        CreateNodeRequest|UpdateNodeRequest $request,
        Node $node,
    ): void {
        $details = $request->data;
        $attributes = $request->data['attributes'];

        $node->setStatus($details['status']);
        $node->persistPurposes($this->canImposePurpose, ...$details['purposes']);
        $node->publish(
            new ImmutableDateTime($details['published_at']),
            $details['published_to'] ? new ImmutableDateTime($details['published_to']) : null
        );

        /*if ($details['parent_id']) {
            $node->moveAsChildOf($this->repository->referenceTo($details['parent_id']));
        } else {*/
        $node->moveAsRootNode();
        //}

        $attributes = $this->processAttributes($attributes);

        $node->persistTermsAssignations($this->parentTermsResolver, ...$this->collectTaxonomiesTerms($node->getNodeType(), $attributes));

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

    /**
     * @param Attribute[] $attributes
     */
    private function collectTaxonomiesTerms(string $nodeType, array $attributes): array
    {
        $contentType = $this->contentTypeRegistry->get($nodeType);
        $result = [];

        foreach ($contentType->getFields() as $field) {
            if ($field->getType() !== 'taxonomy') {
                continue;
            }

            $taxonomy = $field->getConfig('taxonomy');

            $terms = iterator_to_array($attributes[$field->getCode()]->getValue());

            foreach ($terms as $term) {
                $result[] = [$term, $taxonomy];
            }
        }

        return $result;
    }
}
