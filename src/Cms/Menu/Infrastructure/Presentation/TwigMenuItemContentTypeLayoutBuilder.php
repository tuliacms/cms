<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Presentation;

use Symfony\Component\Form\FormView;
use Tulia\Cms\Content\Type\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\Content\Type\Infrastructure\Framework\Form\Service\LayoutTypeBuilderInterface;
use Tulia\Component\Templating\View;

/**
 * @author Adam Banaszkiewicz
 */
class TwigMenuItemContentTypeLayoutBuilder implements LayoutTypeBuilderInterface
{
    public function editorView(ContentType $contentType, FormView $formView, array $viewContext): View
    {
        return new View('@backend/menu/content_builder/menu-item.editor.tpl', [
            'contentType' => $contentType,
            'form' => $formView,
            'context' => $viewContext,
            'locale' => $viewContext['website']->getLocale()->getCode(),
            'websiteId' => $viewContext['website']->getId(),
        ]);
    }

    public function builderView(string $contentType, array $data, array $errors, bool $creationMode): View
    {
        return new View('noop.tpl');
    }
}
