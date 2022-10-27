<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\UserInterface\Console\Command;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteRegistryInterface;
use Tulia\Cms\Website\Application\UseCase\AddLocale;
use Tulia\Cms\Website\Application\UseCase\AddLocaleRequest;
use Tulia\Cms\Website\Application\UseCase\CopyMachineEnum;
use Tulia\Cms\Website\Domain\WriteModel\Exception\CannotAddLocaleException;
use Tulia\Cms\Website\Domain\WriteModel\Service\TranslationCopyMachine\TranslationsCopyMachineFactory;
use Tulia\Cms\Website\UserInterface\Console\Presentation\TranslationsCopyStreamableProgressbar;

/**
 * @author Adam Banaszkiewicz
 */
#[AsCommand(name: 'website:locale:add', description: 'Adds new locale and copies translations')]
final class AddLocaleCommand extends Command
{
    public function __construct(
        private readonly AddLocale $addLocale,
        private readonly TranslationsCopyMachineFactory $copyMachineFactory,
        private readonly WebsiteRegistryInterface $websiteRegistry,
        private readonly Connection $connection,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('locale', InputArgument::REQUIRED, 'New locale CODE');
        $this->addOption('website', null, InputOption::VALUE_REQUIRED, 'Website ID');
        $this->addOption('domain', 'd', InputOption::VALUE_OPTIONAL, 'Domain of this locale, if should be different than of default locale');
        $this->addOption('domainDevelopment', 'ddev', InputOption::VALUE_OPTIONAL, 'Development domain of this locale, if should be different than of default locale');
        $this->addOption('localePrefix', 'lp', InputOption::VALUE_OPTIONAL, 'Locale prefix. Left empty to take care by system');
        $this->addOption('pathPrefix', 'pp', InputOption::VALUE_OPTIONAL, 'Path prefix');
        $this->addOption('sslMode', 'ssl', InputOption::VALUE_OPTIONAL, 'Path prefix', 'ALLOWED_BOTH');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // @todo Maybe we can try to move it to separate Application service? But how implements Progressbar to be UI independent?
        $this->connection->beginTransaction();

        try {
            ($this->addLocale)(
                new AddLocaleRequest(
                    $input->getOption('website'),
                    $input->getArgument('locale'),
                    $input->getOption('domain'),
                    $input->getOption('domainDevelopment'),
                    $input->getOption('localePrefix'),
                    $input->getOption('pathPrefix'),
                    $input->getOption('sslMode'),
                    copyMachineMode: CopyMachineEnum::DISABLE_PROCESSING,
                )
            );

            $io->info('Locale has been added. Copying translations in process, please be patient, this operation may took a while...');

            $copyMachine = $this->copyMachineFactory->create(
                $input->getOption('website'),
                $this->websiteRegistry->get($input->getOption('website'))->getDefaultLocale()->getCode(),
                new TranslationsCopyStreamableProgressbar($input, $output),
            );

            $copyMachine->copyTo($input->getArgument('locale'));

            $this->connection->commit();
        } catch (\Throwable $e) {
            $this->connection->rollBack();

            if ($e instanceof CannotAddLocaleException) {
                $io->error($e->reason);
            }

            throw $e;
        }

        $io->success('New locale successfully added.');
        return Command::SUCCESS;
    }
}
