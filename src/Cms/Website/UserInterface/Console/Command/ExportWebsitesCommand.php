<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\UserInterface\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tulia\Cms\Website\Application\Service\WebsiteDynamicConfiguration;

/**
 * @author Adam Banaszkiewicz
 */
final class ExportWebsitesCommand extends Command
{
    protected static $defaultName = 'website:config:export';

    public function __construct(
        private readonly WebsiteDynamicConfiguration $configuration
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->configuration->update();

        $output->writeln('<info>Websites exported to dynamic configuration/</info>');

        return Command::SUCCESS;
    }
}
