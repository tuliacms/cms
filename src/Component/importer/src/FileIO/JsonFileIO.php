<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\FileIO;

use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Adam Banaszkiewicz
 */
class JsonFileIO implements FileIOInterface
{
    public function __construct(
        private readonly Filesystem $filesystem,
    ) {
    }

    public function supports(string $filepath): bool
    {
        return pathinfo($filepath, PATHINFO_EXTENSION) === 'json';
    }

    public function read(string $filepath): array
    {
        return json_decode(file_get_contents($filepath), true, JSON_THROW_ON_ERROR);
    }

    public function write(string $filepath, array $data): void
    {
        $this->ensureDirectoryExists(dirname($filepath));

        file_put_contents($filepath, json_encode($data, JSON_PRETTY_PRINT));
    }

    public function ensureDirectoryExists(string $directory): void
    {
        if (!is_dir($directory)) {
            if (!mkdir($directory, 0777, true) && !is_dir($directory)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $directory));
            }
        }
    }

    public function ensureDirectoryDoesNotExists(string $directory): void
    {
        $this->filesystem->remove($directory);
    }
}
