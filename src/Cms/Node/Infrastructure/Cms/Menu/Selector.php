<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Cms\Menu;

use Symfony\Component\Form\FormFactoryInterface;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Menu\Domain\Builder\Type\TypeInterface;
use Tulia\Cms\Menu\UserInterface\Web\Backend\Selector\SelectorInterface;
use Tulia\Component\Templating\EngineInterface;
use Tulia\Component\Templating\View;

/**
 * @author Adam Banaszkiewicz
 */
class Selector implements SelectorInterface
{
    public function __construct(
        protected ContentTypeRegistryInterface $contentTypeRegistry,
        protected EngineInterface $engine,
        protected FormFactoryInterface $formFactory,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function render(TypeInterface $type, ?string $identity, string $websiteId, string $locale): string
    {
        [, $name] = explode(':', $type->getType());
        $field = 'node_search_' . $name;

        $nodeType = $this->contentTypeRegistry->get($name);
        $form = $this->formFactory->create(MenuItemSelectorForm::class, [
            $field => $identity,
        ], [
            'node_type' => $nodeType,
            'locale' => $locale,
            'website_id' => $websiteId,
        ]);

        return $this->engine->render(new View('@backend/node/menu/selector.tpl', [
            'form' => $form->createView(),
            'type' => $nodeType->getCode(),
            'field' => $field,
            'identityType' => $type,
        ]));
    }
}
