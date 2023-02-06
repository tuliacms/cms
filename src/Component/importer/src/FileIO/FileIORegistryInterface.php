<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\FileIO;

use Tulia\Component\Importer\Exception\FileNotSupportedException;

/**
 * @author Adam Banaszkiewicz
 */
interface FileIORegistryInterface
{
    public function addReader(FileIOInterface $reader);

    /**
     * @return FileIOInterface[]
     */
    public function all(): array;

    /**
     * @throws FileNotSupportedException
     */
    public function getSupported(string $filepath): FileIOInterface;
}
