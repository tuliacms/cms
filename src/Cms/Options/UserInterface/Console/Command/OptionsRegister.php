<?php

declare(strict_types=1);

namespace Tulia\Cms\Options\UserInterface\Console\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tulia\Cms\Options\Application\UseCase\RegisterMissingOptions;
use Tulia\Cms\Options\Application\UseCase\RegisterMissingOptionsRequest;

/**
 * @author Adam Banaszkiewicz
 */
#[AsCommand(name: 'options:register')]
class OptionsRegister extends Command
{
    public function __construct(
        private readonly RegisterMissingOptions $registerMissingOptions
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Register all available, not registered options in system, for given website.')
            ->addOption(
                'websites',
                'w',
                InputOption::VALUE_OPTIONAL,
                'Website ID list (separated by comma) to which to save options. If empty - register for all available websites.'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        ($this->registerMissingOptions)(new RegisterMissingOptionsRequest(
            array_filter(explode(',', (string) $input->getOption('websites')))
        ));

        $output->writeln('<info>Missing options successfully registered.</info>');

        return Command::SUCCESS;
    }
}
