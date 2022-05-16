<?php

declare(strict_types=1);

namespace Tulia\Cms\TuliaEditor\UserInterface\Web\Backend\Controller;

use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Editor extends AbstractController
{
    public function editor(): ViewInterface
    {
        return $this->view('@backend/tulia-editor/editor-window.tpl');
    }
    public function preview(): ViewInterface
    {
        return $this->view('@backend/tulia-editor/preview-window.tpl');
    }
}
