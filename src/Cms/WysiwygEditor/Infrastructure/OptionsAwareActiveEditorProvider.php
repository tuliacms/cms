<?php

declare(strict_types=1);

namespace Tulia\Cms\WysiwygEditor\Infrastructure;

use Tulia\Cms\Options\Domain\ReadModel\Options;
use Tulia\Cms\WysiwygEditor\Application\ActiveEditorProviderInterface;
use Tulia\Cms\WysiwygEditor\Application\DefaultEditor;
use Tulia\Cms\WysiwygEditor\Application\RegistryInterface;
use Tulia\Cms\WysiwygEditor\Application\WysiwygEditorInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class OptionsAwareActiveEditorProvider implements ActiveEditorProviderInterface
{
    public function __construct(
        private readonly Options $options,
        private readonly RegistryInterface $registry,
    ) {
    }

    public function get(): WysiwygEditorInterface
    {
        $id = $this->options->get('wysiwyg_editor');

        if ($this->registry->has($id)) {
            return $this->registry->get($id);
        }

        return new DefaultEditor();
    }
}
