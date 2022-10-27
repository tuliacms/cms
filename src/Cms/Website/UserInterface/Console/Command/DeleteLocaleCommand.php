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
use Tulia\Cms\Security\Application\Service\AuthenticatedUserPasswordValidator;
use Tulia\Cms\Website\Application\UseCase\CopyMachineEnum;
use Tulia\Cms\Website\Application\UseCase\DeleteLocale;
use Tulia\Cms\Website\Application\UseCase\DeleteLocaleRequest;
use Tulia\Cms\Website\Domain\WriteModel\Exception\CannotDeleteLocaleException;
use Tulia\Cms\Website\Domain\WriteModel\Service\TranslationCopyMachine\TranslationsCopyMachineFactory;
use Tulia\Cms\Website\UserInterface\Console\Presentation\TranslationsCopyStreamableProgressbar;

/**
 * @author Adam Banaszkiewicz
 */
#[AsCommand(name: 'website:locale:delete', description: 'Delete locale and it\'s translations')]
final class DeleteLocaleCommand extends Command
{
    public function __construct(
        private readonly DeleteLocale $deleteLocale,
        private readonly TranslationsCopyMachineFactory $copyMachineFactory,
        private readonly WebsiteRegistryInterface $websiteRegistry,
        private readonly Connection $connection,
        private readonly AuthenticatedUserPasswordValidator $passwordValidator,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('website', null, InputOption::VALUE_REQUIRED, 'Website ID');
        $this->addArgument('locale', InputArgument::REQUIRED, 'Locale CODE to delete');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // @todo Maybe we can try to move it to separate Application service? But how implements Progressbar to be UI independent?
        $this->connection->beginTransaction();

        try {
            ($this->deleteLocale)(
                new DeleteLocaleRequest(
                    $input->getOption('website'),
                    $input->getArgument('locale'),
                    copyMachineMode: CopyMachineEnum::DISABLE_PROCESSING,
                )
            );

            $io->info('Locale has been deleted. Deleting translations in process, please be patient, this operation may took a while...');

            $copyMachine = $this->copyMachineFactory->create(
                $input->getOption('website'),
                $this->websiteRegistry->get($input->getOption('website'))->getDefaultLocale()->getCode(),
                new TranslationsCopyStreamableProgressbar($input, $output),
            );

            $copyMachine->deleteFrom($input->getArgument('locale'));

            $this->connection->commit();
        } catch (\Throwable $e) {
            $this->connection->rollBack();

            if ($e instanceof CannotDeleteLocaleException) {
                $io->error($e->reason);
            }

            throw $e;
        }

        $io->success('Locale successfully deleted.');
        return Command::SUCCESS;
    }
}
