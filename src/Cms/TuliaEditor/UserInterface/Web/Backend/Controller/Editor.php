<?php

declare(strict_types=1);

namespace Tulia\Cms\TuliaEditor\UserInterface\Web\Backend\Controller;

use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Content\Attributes\Domain\ReadModel\Service\ValueRendering\ValueRendererInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Security\Framework\Security\Http\Csrf\Annotation\IgnoreCsrfToken;
use Tulia\Component\Shortcode\ProcessorInterface;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Editor extends AbstractController
{
    public function __construct(
        private readonly ValueRendererInterface $renderer,
        private readonly ProcessorInterface $processor,
    ) {
    }

    public function editor(): ViewInterface
    {
        return $this->view('@backend/tulia-editor/editor-window.tpl');
    }

    /**
     * @IgnoreCsrfToken()
     */
    public function preview(Request $request): ViewInterface
    {
        if ($request->isMethod('POST')) {
            $content = $request->request->get('content');
            $content = $this->processor->process($content);
            $content = $this->renderer->render($content, ['attribute' => 'Tulia Editor dynamic preview']);
        } else {
            $content = '';
        }

        return $this->view('@backend/tulia-editor/preview-window.tpl', [
            'content' => $content,
            'source' => $request->request->get('content'),
        ]);
    }
}
