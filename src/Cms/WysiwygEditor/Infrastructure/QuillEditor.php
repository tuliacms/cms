<?php

declare(strict_types=1);

namespace Tulia\Cms\WysiwygEditor\Infrastructure;

use Tulia\Cms\WysiwygEditor\Application\WysiwygEditorInterface;
use Tulia\Component\Templating\EngineInterface;
use Tulia\Component\Templating\View;

/**
 * @author Adam Banaszkiewicz
 */
final class QuillEditor implements WysiwygEditorInterface
{
    public function __construct(
        private readonly EngineInterface $engine,
    ) {
    }

    public function getId(): string
    {
        return 'quill';
    }

    public function getName(): string
    {
        return 'Quill Editor';
    }

    public function render(string $name, ?string $content = null, array $params = []): string
    {
        if (isset($params['id']) === false) {
            $params['id'] = uniqid('', true);
        }

        return $this->engine->render(new View('@backend/wysiwyg/quill-editor.tpl', [
            'name'    => $name,
            'content' => $content,
            'params'  => $params,
        ]));
    }
}
