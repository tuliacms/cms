<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Cms\Menu;

use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Menu\Domain\Builder\Type\RegistratorInterface;
use Tulia\Cms\Menu\Domain\Builder\Type\RegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class TaxonomyTermMenuItemTypeRegistrator implements RegistratorInterface
{
    public function __construct(
        private readonly ContentTypeRegistryInterface $contentTypeRegistry,
        private readonly Selector $selector,
    ) {
    }

    public function register(RegistryInterface $registry): void
    {
        foreach ($this->contentTypeRegistry->allByType('taxonomy') as $contentType) {
            $type = $registry->registerType('taxonomy:' . $contentType->getCode());
            $type->setLabel($contentType->getName());
            $type->setTranslationDomain('taxonomy');
            $type->setSelectorService($this->selector);
        }
    }
}
