<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\FileIO;

/**
 * @author Adam Banaszkiewicz
 */
interface FileIOInterface
{
    public function supports(string $filepath): bool;

    public function read(string $filepath): array;

    public function write(string $filepath, array $data): void;
}
