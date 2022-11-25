<?php

declare(strict_types=1);

namespace Tulia\Cms\TuliaEditor\Infrastructure\Framework\Twig\Extension;

use Tulia\Component\Templating\EngineInterface;
use Tulia\Component\Templating\View;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class TuliaEditorExtension extends AbstractExtension
{
    public function __construct(
        private readonly EngineInterface $engine,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('tulia_editor', function (string $name, ?string $content, array $params = []) {
                if (isset($params['id']) === false) {
                    $params['id'] = uniqid('', true);
                }

                return $this->engine->render(new View('@backend/tulia-editor/editor-control.tpl', [
                    'name' => $name,
                    'content' => $content,
                    'params' => $params,
                ]));
            }, [
                'is_safe' => [ 'html' ]
            ]),
        ];
    }
}
