<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\UserInterface\Console\Command;

use DirectoryIterator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Tulia\Cms\Filemanager\Domain\ImageSize\ImageSizeRegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
#[AsCommand(name: 'filemanager:thumbnails:clear-nonexistent-sizes')]
class FilemanagerThumbnailsClearNonexistentSizesCommand extends Command
{
    private ImageSizeRegistryInterface $imageSizeRegistry;
    private string $publicDirectory;

    public function __construct(
        ImageSizeRegistryInterface $imageSizeRegistry,
        string $publicDirectory
    ) {
        parent::__construct(static::$defaultName);

        $this->imageSizeRegistry = $imageSizeRegistry;
        $this->publicDirectory = $publicDirectory;
    }

    protected function configure()
    {
        $this->addOption('dry-run', 'd', InputOption::VALUE_OPTIONAL, 'Only prints info, without remove files', false);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $fs = new Filesystem();

        $basepath = $this->publicDirectory.'/uploads/thumbnails';

        foreach (new DirectoryIterator($basepath) as $dir) {
            if ($dir->isDot()) {
                continue;
            }

            if ($input->getOption('dry-run')) {
                if ($this->imageSizeRegistry->has($dir->getFilename())) {
                    $io->warning(sprintf('Size %s found in files and in System configuration.', $dir->getFilename()));
                } else {
                    $io->info(sprintf('Size %s found in files, but not exists in System configuration. Files should be removed.', $dir->getFilename()));
                }
            } else {
                if ($this->imageSizeRegistry->has($dir->getFilename()) === false) {
                    $fs->remove($dir->getPathname());
                    $io->info(sprintf('Size %s not found in system. All files were removed.', $dir->getFilename()));
                }
            }
        }

        return Command::SUCCESS;
    }
}
