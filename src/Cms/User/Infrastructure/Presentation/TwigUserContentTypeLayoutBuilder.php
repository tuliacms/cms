<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Presentation;

use Symfony\Component\Form\FormView;
use Tulia\Cms\Content\Type\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\Content\Type\Infrastructure\Framework\Form\Service\LayoutTypeBuilderInterface;
use Tulia\Component\Templating\View;

/**
 * @author Adam Banaszkiewicz
 */
class TwigUserContentTypeLayoutBuilder implements LayoutTypeBuilderInterface
{
    public function editorView(ContentType $contentType, FormView $formView, array $viewContext): View
    {
        return new View('@backend/user/content_builder/user.editor.tpl', [
            'contentType' => $contentType,
            'form' => $formView,
            'context' => $viewContext,
        ]);
    }

    public function builderView(string $contentType, array $data, array $errors, bool $creationMode): View
    {
        return new View('noop.tpl');
    }
}
