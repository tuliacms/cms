<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Cms\EditLinks;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\EditLinks\Model\Collection;
use Tulia\Cms\EditLinks\Service\EditLinksCollectorInterface;
use Tulia\Cms\Node\Domain\ReadModel\Model\Node;

/**
 * @author Adam Banaszkiewicz
 */
class EditLinksRegistrator implements EditLinksCollectorInterface
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly RouterInterface $router,
        private readonly ContentTypeRegistryInterface $registry,
    ) {
    }

    public function collect(Collection $collection, object $node, array $options = []): void
    {
        try {
            $type = $this->registry->get($node->getType());

            $collection->add('node.edit', [
                'link'  => $this->router->generate('backend.node.edit', [ 'node_type' => $node->getType(), 'id' => $node->getId() ]),
                'label' => $this->translator->trans('editNode', [
                    'node' => mb_strtolower($this->translator->trans($type->getName(), [], 'node')),
                ]),
            ]);
        } catch (\Exception $e) {
            // Do nothing when Node Type not exists.
        }
    }

    public function supports(object $object): bool
    {
        return $object instanceof Node;
    }
}
