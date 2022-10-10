<?php

declare(strict_types=1);

namespace Tulia\Cms\Deployment\UserInterface\Console\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Adam Banaszkiewicz
 */
#[AsCommand(name: 'deployer:detect')]
final class DeployerDetect extends Command
{
    public function __construct(
        private readonly string $projectDir,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Detects if Deployer is installed with Composer.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!file_exists($this->projectDir.'/vendor/deployer/deployer/src/Deployer.php')) {
            $io = new SymfonyStyle($input, $output);
            $io->writeln('<fg=red>You need to install Deployer first.</>');
            $io->writeln('To install Deployer execute those commands one by one:');
            $io->writeln('   1. <fg=gray>make bash</>');
            $io->writeln('   2. <fg=gray>composer require deployer/deployer --dev</>');
            $io->writeln('   3. <fg=gray>exit</>');
            $io->writeln('Then configure deployer with command:');
            $io->writeln('   1. <fg=gray>make console make:deployer</>');
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
