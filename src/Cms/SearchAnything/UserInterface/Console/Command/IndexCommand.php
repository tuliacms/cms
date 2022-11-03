<?php

declare(strict_types=1);

namespace Tulia\Cms\SearchAnything\UserInterface\Console\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteRegistryInterface;
use Tulia\Cms\SearchAnything\Application\UseCase\Index;
use Tulia\Cms\SearchAnything\Application\UseCase\IndexRequest;

/**
 * @author Adam Banaszkiewicz
 */
#[AsCommand(name: 'search-anything:index')]
final class IndexCommand extends Command
{
    public function __construct(
        private readonly WebsiteRegistryInterface $websiteRegistry,
        private readonly Index $index,
    ) {
        parent::__construct(null);
    }

    protected function configure()
    {
        $this->addOption('website', null, InputOption::VALUE_OPTIONAL, 'Website ID');
        $this->addOption('everything', null, InputOption::VALUE_NONE, 'Executes indexing for all existing websites');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $everything = $input->getOption('everything');
        $website = $input->getOption('website');

        if (!$everything && !$website) {
            throw new \InvalidArgumentException('You must specify one --website or --everything option.');
        }

        if ($everything) {
            $websites = $this->websiteRegistry->all();
        } else {
            $websites = [$this->websiteRegistry->get($website)];
        }

        foreach ($websites as $website) {
            $output->writeln(sprintf('Indexing website %s', $website->getId()));

            ($this->index)(new IndexRequest($website->getId()));
        }

        $output->writeln('Done.');
        return Command::SUCCESS;
    }
}
