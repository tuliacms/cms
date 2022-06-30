<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\ModuleIntegration\Menu;

use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Menu\Domain\Builder\Type\RegistratorInterface;
use Tulia\Cms\Menu\Domain\Builder\Type\RegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class NodeMenuItemTypeRegistrator implements RegistratorInterface
{
    private ContentTypeRegistryInterface $contentTypeRegistry;

    private Selector $selector;

    public function __construct(ContentTypeRegistryInterface $contentTypeRegistry, Selector $selector)
    {
        $this->contentTypeRegistry = $contentTypeRegistry;
        $this->selector = $selector;
    }

    /**
     * {@inheritdoc}
     */
    public function register(RegistryInterface $registry): void
    {
        foreach ($this->contentTypeRegistry->all() as $nodeType) {
            if ($nodeType->isType('node')) {
                $type = $registry->registerType('node:' . $nodeType->getCode());
                $type->setLabel($nodeType->getName());
                $type->setSelectorService($this->selector);
            }
        }
    }
}
