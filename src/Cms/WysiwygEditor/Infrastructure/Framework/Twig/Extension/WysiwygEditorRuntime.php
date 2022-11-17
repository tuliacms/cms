<?php

declare(strict_types=1);

namespace Tulia\Cms\WysiwygEditor\Infrastructure\Framework\Twig\Extension;

use Tulia\Cms\WysiwygEditor\Application\ActiveEditorProviderInterface;
use Twig\Extension\RuntimeExtensionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class WysiwygEditorRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private readonly ActiveEditorProviderInterface $provider,
    ) {
    }

    public function wysiwygEditor(string $name, ?string $content, array $params = []): string
    {
         return $this->provider->get()->render($name, $content, $params);
    }
}
