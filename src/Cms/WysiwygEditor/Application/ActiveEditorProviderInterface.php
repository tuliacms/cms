<?php

declare(strict_types=1);

namespace Tulia\Cms\WysiwygEditor\Application;

/**
 * @author Adam Banaszkiewicz
 */
interface ActiveEditorProviderInterface
{
    public function get(): WysiwygEditorInterface;
}
