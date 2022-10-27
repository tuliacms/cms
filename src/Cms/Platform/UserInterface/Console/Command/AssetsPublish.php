<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\UserInterface\Console\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tulia\Cms\Platform\Application\Service\AssetsPublisher;

/**
 * @author Adam Banaszkiewicz
 */
#[AsCommand(name: 'assets:publish', description: 'Publish assets to /public directory.')]
class AssetsPublish extends Command
{
    private AssetsPublisher $assetsPublisher;
    private array $assetsPublicPaths;
    private string $rootDir;

    public function __construct(AssetsPublisher $assetsPublisher, array $assetsPublicPaths, string $rootDir)
    {
        $this->assetsPublisher = $assetsPublisher;
        $this->assetsPublicPaths = $assetsPublicPaths;
        $this->rootDir = $rootDir;

        parent::__construct(self::$defaultName);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $quiet = $input->getOption('quiet');

        foreach ($this->assetsPublicPaths as $path) {
            $sourceHumanized = str_replace($this->rootDir, '', $path['source']);
            $targetHumanized = '/public/assets' . $path['target'];

            $status = $this->assetsPublisher->publish($path['source'], $path['target']);

            if (!$quiet) {
                if ($status) {
                    $output->writeln(sprintf('<info>%s => %s</info>', $sourceHumanized, $targetHumanized));
                } else {
                    $output->writeln(sprintf('<fg=red>%s => %s</>', $sourceHumanized, $targetHumanized));
                }
            }
        }

        return Command::SUCCESS;
    }
}
