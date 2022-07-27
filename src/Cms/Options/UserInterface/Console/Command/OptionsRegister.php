<?php

declare(strict_types=1);

namespace Tulia\Cms\Options\UserInterface\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tulia\Cms\Options\Application\Service\WebsitesOptionsRegistrator;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\Model\Collection;
use Tulia\Cms\Website\Domain\ReadModel\Finder\WebsiteFinderInterface;
use Tulia\Cms\Website\Domain\ReadModel\Finder\WebsiteFinderScopeEnum;
use Tulia\Component\Routing\Website\WebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class OptionsRegister extends Command
{
    protected static $defaultName = 'options:register';

    public function __construct(
        private WebsiteFinderInterface $websiteFinder,
        private WebsitesOptionsRegistrator $optionsRegistrator,
        private WebsiteInterface $website
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('options:register')
            ->setDescription('Register all available, not registered options in system, for given website.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->optionsRegistrator->registerMissingOptions();

        $output->writeln('<info>Missing options successfully registered.</info>');

        return Command::SUCCESS;
    }

    private function fetchWebsites(?string $websiteIdSource): Collection
    {
        $websiteIdList = array_filter(explode(',', (string) $websiteIdSource));
        $criteria = [];

        if ($websiteIdList !== []) {
            $criteria = ['id__in' => $websiteIdList];
        }

        return $this->websiteFinder->find($criteria, WebsiteFinderScopeEnum::INTERNAL);
    }
}
