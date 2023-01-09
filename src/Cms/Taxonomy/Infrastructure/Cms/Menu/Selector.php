<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Cms\Menu;

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
        private readonly ContentTypeRegistryInterface $contentTypeRegistry,
        private readonly EngineInterface $engine,
        private readonly FormFactoryInterface $formFactory,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function render(TypeInterface $type, ?string $identity, string $websiteId, string $locale): string
    {
        [, $name] = explode(':', $type->getType());
        $field = 'term_search_' . $name;

        $contentType = $this->contentTypeRegistry->get($name);
        $form = $this->formFactory->create(MenuItemSelectorForm::class, [
            $field => $identity,
        ], [
            'taxonomy_type' => $contentType,
            'locale' => $locale,
            'website_id' => $websiteId,
        ]);

        return $this->engine->render(new View('@backend/taxonomy/menu/selector.tpl', [
            'form' => $form->createView(),
            'type' => $contentType->getCode(),
            'field' => $field,
            'identityType' => $type,
        ]));
    }
}
