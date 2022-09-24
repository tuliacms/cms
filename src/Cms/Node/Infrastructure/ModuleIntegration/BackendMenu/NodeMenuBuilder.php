<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\ModuleIntegration\BackendMenu;

use Tulia\Cms\BackendMenu\Builder\BuilderInterface;
use Tulia\Cms\BackendMenu\Builder\Helper\BuilderHelperInterface;
use Tulia\Cms\BackendMenu\Builder\Registry\ItemRegistryInterface;
use Tulia\Cms\Content\Type\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class NodeMenuBuilder implements BuilderInterface
{
    public function __construct(
        private readonly BuilderHelperInterface $helper,
        private readonly ContentTypeRegistryInterface $contentTypeRegistry,
    ) {
    }

    public function build(ItemRegistryInterface $registry, string $websiteId, string $locale): void
    {
        foreach ($this->contentTypeRegistry->all() as $type) {
            if ($type->isType('node')) {
                $this->registerContentType($registry, $type);
            }
        }
    }

    private function registerContentType(ItemRegistryInterface $registry, ContentType $type): void
    {
        $root = 'node_' . $type->getCode();

        $registry->add($root, [
            'label'    => $this->helper->trans($type->getName(), [], 'node'),
            'link'     => '#',
            'icon'     => $type->getIcon(),
            'priority' => 3500,
            'parent'   => 'section_contents',
        ]);

        $registry->add($root . '_item', [
            'label'  => $this->helper->trans('nodesListOfType', ['type' => $this->helper->trans($type->getName(), [], 'node')], 'node'),
            'link'   => $this->helper->generateUrl('backend.node', [ 'node_type' => $type->getCode() ]),
            'parent' => $root,
        ]);

        // TODO Finish listing of taxonomies attached to Type
        /*foreach ($type->getFields() as $field) {
            if ($field->getType() !== 'taxonomy') {
                continue;
            }

            $taxonomy = $this->contentTypeRegistry->get($field->getTaxonomy());

            $registry->add($root . '_' . $taxonomy->getCode(), [
                'label'  => $this->helper->trans('termsListOfTaxonomy', ['taxonomy' => $this->helper->trans($taxonomy->getName(), [], 'taxonomy')], 'taxonomy'),
                'link'   => $this->helper->generateUrl('backend.term', [ 'taxonomyType' => $taxonomy->getCode() ]),
                'parent' => $root,
            ]);
        }*/
    }
}
