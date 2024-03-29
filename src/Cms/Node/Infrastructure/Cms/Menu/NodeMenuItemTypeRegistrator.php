<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Cms\Menu;

use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Menu\Domain\Builder\Type\RegistratorInterface;
use Tulia\Cms\Menu\Domain\Builder\Type\RegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class NodeMenuItemTypeRegistrator implements RegistratorInterface
{
    public function __construct(
        private readonly ContentTypeRegistryInterface $contentTypeRegistry,
        private readonly Selector $selector,
    ) {
    }

    public function register(RegistryInterface $registry): void
    {
        foreach ($this->contentTypeRegistry->allByType('node') as $nodeType) {
            $type = $registry->registerType('node:' . $nodeType->getCode());
            $type->setLabel($nodeType->getName());
            $type->setTranslationDomain('node');
            $type->setSelectorService($this->selector);
        }
    }
}
