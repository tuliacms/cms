<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Infrastructure\Persistence\Filesystem;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Tulia\Cms\Filemanager\Domain\WriteModel\Service\FileStorageInterface;
use Tulia\Cms\Shared\Domain\WriteModel\Service\SluggerInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class FilesystemFileStorage implements FileStorageInterface
{
    public function __construct(
        private readonly string $publicDirectory,
        private readonly SluggerInterface $slugger,
    ) {
    }

    public function store(string $source, string $destination): \SplFileInfo
    {
        $file = new File($source);

        $originalFilename = pathinfo($file->getFilename(), PATHINFO_FILENAME);
        $extension = strtolower($file->guessExtension() ?? pathinfo($file->getFilename(), PATHINFO_EXTENSION));

        $destination = $this->publicDirectory . $destination;

        $safeFilename = $this->slugger->filename($originalFilename);
        $safeFilename = $this->uniqueName($destination, $safeFilename, $extension);
        $newFilename = $safeFilename . '.' . $extension;

        if (is_dir($destination) === false) {
            if (! mkdir($destination, 0777, true) && !is_dir($destination)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $destination));
            }
        }

        if (!is_writable($destination)) {
            throw new \RuntimeException(sprintf('Directory "%s" is not writable.', $destination));
        }

        $fs = new Filesystem();
        $fs->copy($source, $destination.'/'.$newFilename);

        return new File($destination.'/'.$newFilename);
    }

    private function uniqueName(string $directory, string $filename, string $extension): string
    {
        if (is_file($directory . '/' . $filename . '.' . $extension) === false) {
            return $filename;
        }

        $iteration = 1;

        do {
            $newFilename = $filename . '-' . $iteration;

            if (is_file($directory . '/' . $newFilename . '.' . $extension) === false) {
                return $newFilename;
            }

            $iteration++;
        } while(true);
    }
}
