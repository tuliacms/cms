<?php

declare(strict_types=1);

namespace Tulia\Cms\WysiwygEditor\Application;

/**
 * @author Adam Banaszkiewicz
 */
class Registry implements RegistryInterface
{
    public function __construct(
        private readonly iterable $editors,
    ) {
    }

    public function has(string $id): bool
    {
        foreach ($this->editors as $editor) {
            if ($editor->getId() === $id) {
                return true;
            }
        }

        return false;
    }

    public function get(string $id): WysiwygEditorInterface
    {
        foreach ($this->editors as $editor) {
            if ($editor->getId() === $id) {
                return $editor;
            }
        }

        throw new \OutOfBoundsException(sprintf('Editor %s does not exists.', $id));
    }

    public function getEditors(): iterable
    {
        return $this->editors;
    }
}
