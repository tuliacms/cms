<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\UserInterface\Web\Shared\Form\FormType;

use Tulia\Cms\Content\Type\Domain\ReadModel\FieldChoicesProvider\FieldChoicesProviderInterface;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;

/**
 * @author Adam Banaszkiewicz
 * @final
 * @lazy
 */
class TaxonomyTypeaheadTypeChoicesProvider implements FieldChoicesProviderInterface
{
    public function __construct(
        private readonly ContentTypeRegistryInterface $contentTypeRegistry,
    ) {
    }

    public function provide(): array
    {
        $result = [];

        foreach ($this->contentTypeRegistry->allByType('taxonomy') as $item) {
            $result[] = [ 'value' => $item->getCode(), 'label' => $item->getName() ];
        }

        return $result;
    }
}
