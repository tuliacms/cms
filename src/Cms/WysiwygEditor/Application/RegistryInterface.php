<?php

declare(strict_types=1);

namespace Tulia\Cms\WysiwygEditor\Application;

/**
 * @author Adam Banaszkiewicz
 */
interface RegistryInterface
{
    public function getEditors(): iterable;

    public function has(string $id): bool;

    public function get(string $id): WysiwygEditorInterface;
}
