<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\ZipArchive;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * @author Adam Banaszkiewicz
 */
final class ZipArchive
{
    private function __construct(
        private readonly string $filepath,
        private readonly \ZipArchive $archive,
    ) {
    }

    public static function create(string $filepath): self
    {
        $zip = new \ZipArchive();

        if ($zip->open($filepath, \ZipArchive::CREATE) !== true) {
            throw new \RuntimeException("cannot open <$filepath>\n");
        }

        return new self($filepath, $zip);
    }

    public static function open(string $filepath): self
    {
        $zip = new \ZipArchive();

        if ($zip->open($filepath) !== true) {
            throw new \RuntimeException("cannot open <$filepath>\n");
        }

        return new self($filepath, $zip);
    }

    public function addFilesFrom(string $directory): void
    {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, \strlen($directory) + 1);

                $this->archive->addFile($filePath, $relativePath);
            }
        }
    }

    public function close(): void
    {
        $this->archive->close();
    }

    public function extractTo(string $directory): void
    {
        if (!is_dir($directory)) {
            if (!mkdir($directory, 0777, true) && !is_dir($directory)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $directory));
            }
        }

        $this->archive->extractTo($directory);
    }
}
