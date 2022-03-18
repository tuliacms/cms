<?php

declare(strict_types=1);

namespace Tulia\Cms\TuliaEditor\UserInterface\Web\Backend\Controller;

use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Editor extends AbstractController
{
    public function editor(Request $request): ViewInterface
    {
        return $this->view('@backend/tulia-editor/editor-window.tpl');
    }
}
