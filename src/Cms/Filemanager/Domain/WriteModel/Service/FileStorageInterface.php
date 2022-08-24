<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Domain\WriteModel\Service;

/**
 * @author Adam Banaszkiewicz
 */
interface FileStorageInterface
{
    public function store(string $source, string $destination): \SplFileInfo;
}
