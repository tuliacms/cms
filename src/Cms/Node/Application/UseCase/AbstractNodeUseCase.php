<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Application\UseCase;

use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Node\Domain\WriteModel\Model\Attribute;
use Tulia\Cms\Node\Domain\WriteModel\Model\Node;
use Tulia\Cms\Node\Domain\WriteModel\Rules\CanAddPurpose\CanImposePurposeInterface;
use Tulia\Cms\Node\Domain\WriteModel\Service\NodeOptionsInterface;
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
        protected readonly NodeOptionsInterface $options,
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

        $categoryTaxonomy = $this->options->get('category_taxonomy', $node->getNodeType());

        if ($categoryTaxonomy) {
            if ($request->data['main_category']) {
                $node->assignToMainCategory($this->parentTermsResolver, $request->data['main_category'], $categoryTaxonomy);
            } else {
                $node->unassignFromMainCategory($this->parentTermsResolver);
            }

            $node->persistAdditionalCategoriesAssignations($this->parentTermsResolver, ...$this->collectTaxonomiesTerms($request->data['additional_categories'] ?? [], $categoryTaxonomy));
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

    private function collectTaxonomiesTerms(array $categories, string $taxonomy): array
    {
        $result = [];

        foreach ($categories as $term) {
            $result[] = [$term, $taxonomy];
        }

        return $result;
    }
}
