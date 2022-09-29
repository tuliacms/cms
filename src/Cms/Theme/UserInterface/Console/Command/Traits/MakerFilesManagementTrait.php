<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\UserInterface\Console\Command\Traits;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * @author Adam Banaszkiewicz
 */
trait MakerFilesManagementTrait
{
    private function collectFilesList(string $directory): array
    {
        $finder = new Finder();
        $finder->files()->ignoreDotFiles(false)->in($directory);

        $files = [];

        foreach ($finder as $file) {
            $files[$file->getRelativePathname()] = $file->getContents();
        }

        return $files;
    }

    private function interpolate(array $filesList, array $replacements): array
    {
        foreach ($filesList as $key => $file) {
            unset($filesList[$key]);
            $key = (string) str_replace(array_keys($replacements), array_values($replacements), $key);

            $filesList[$key] = str_replace(array_keys($replacements), array_values($replacements), $file);
        }

        return $filesList;
    }

    private function writeFiles(array $filesList, string $destination): void
    {
        $filesystem = new Filesystem();

        foreach ($filesList as $filename => $content) {
            $filepath = $destination.'/'.$filename;
            $directory = dirname($filepath);

            if (!is_dir($destination)) {
                $filesystem->mkdir($directory);
            }

            $filesystem->dumpFile($filepath, $content);
        }
    }
}
