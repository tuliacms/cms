<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\UserInterface\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * @author Adam Banaszkiewicz
 */
final class FixDirectoriesPermissions extends Command
{
    protected static $defaultName = 'fs:dirs:permissions:fix';

    private array $directories = [
        '/public',
        '/var',
    ];

    public function __construct(
        private readonly string $rootDir
    ) {
        parent::__construct(self::$defaultName);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fs = new Filesystem();

        foreach ($this->directories as $directory) {
            $directories = (new Finder())
                ->directories()
                ->in($this->rootDir.$directory)
            ;
            $fs->chmod($directories, 0777);
        }

        return Command::SUCCESS;
    }
}
