<?php

declare(strict_types=1);

namespace Tulia\Cms\Extension\UserInterface\Web\Controller;

use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Component\Templating\View;

/**
 * @author Adam Banaszkiewicz
 */
final class Modules extends AbstractController
{
    public function index(): View
    {
        /** @var array $modules */
        $modules = $this->getParameter('cms.extensions.modules');
        $root = $this->getParameter('kernel.project_dir');

        foreach ($modules as $key => $val) {
            $modules[$key]['details'] = json_decode(file_get_contents($root.$val['manifest']), true, 512, JSON_THROW_ON_ERROR);
        }
        return $this->view('@backend/extension/module/list.tpl', [
            'modules' => $modules,
        ]);
    }
}
