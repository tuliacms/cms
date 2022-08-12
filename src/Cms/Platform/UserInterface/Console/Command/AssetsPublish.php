<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\UserInterface\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tulia\Cms\Platform\Application\Service\AssetsPublisher;

/**
 * @author Adam Banaszkiewicz
 */
class AssetsPublish extends Command
{
    protected static $defaultName = 'assets:publish';

    private AssetsPublisher $assetsPublisher;
    private array $assetsPublicPaths;
    private string $rootDir;

    public function __construct(AssetsPublisher $assetsPublisher, array $assetsPublicPaths, string $rootDir)
    {
        $this->assetsPublisher = $assetsPublisher;
        $this->assetsPublicPaths = $assetsPublicPaths;
        $this->rootDir = $rootDir;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('assets:publish')
            ->setDescription('Publish assets to /public directory.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->assetsPublicPaths as $path) {
            $sourceHumanized = str_replace($this->rootDir, '', $path['source']);
            $targetHumanized = '/public/assets' . $path['target'];

            if ($this->assetsPublisher->publish($path['source'], $path['target'])) {
                $output->writeln(sprintf('<info>%s => %s</info>', $sourceHumanized, $targetHumanized));
            } else {
                $output->writeln(sprintf('<fg=red>%s => %s</>', $sourceHumanized, $targetHumanized));
            }
        }

        return Command::SUCCESS;
    }
}
