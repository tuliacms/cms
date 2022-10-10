<?php

declare(strict_types=1);

namespace Tulia\Cms\Deployment\UserInterface\Console\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Tulia\Cms\Deployment\Domain\WriteModel\Service\DeploymentFileGeneratorRegistry;

/**
 * @author Adam Banaszkiewicz
 */
#[AsCommand(name: 'make:deployer')]
final class MakeDeployer extends Command
{
    public function __construct(
        private readonly string $projectDir,
        private readonly DeploymentFileGeneratorRegistry $generatorRegistry,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Deployer configurator.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (!file_exists($this->projectDir.'/vendor/deployer/deployer/src/Deployer.php')) {
            $io->writeln('<fg=red>You need to install Deployer first to configure deployments for this instance of Tulia CMS.</>');
            $io->writeln('To install Deployer execute those commands one by one:');
            $io->writeln('   1. <fg=gray>make bash</>');
            $io->writeln('   2. <fg=gray>composer require deployer/deployer --dev</>');
            $io->writeln('   3. <fg=gray>exit</>');
            return Command::FAILURE;
        }

        if (is_file($this->projectDir.'/deploy.php') && !$this->confirm('Deployer config exists, do You want to overwrite it?', $input, $output)) {
            return Command::FAILURE;
        }

        $hosting = $this->askForHosting($input, $output);

        $generator = $this->generatorRegistry->get($hosting);
        $parameters = [
            '[[repository]]' => $this->ask('Git tepository', $input, $output),
        ];

        foreach ($generator->questions() as $key => $question) {
            $parameters['[['.$key.']]'] = $this->getHelper('question')->ask($input, $output, $question);
        }

        file_put_contents($this->projectDir.'/deploy.php', $generator->generate($parameters));

        $io->writeln('<info>Deployer config file created. Next steps:</info>');
        $io->writeln('   1. Fill the <fg=gray>repository</> setting in newly created <fg=gray>deployer.php</> file, with git repository path to this application.');
        $io->writeln('   2. Execute <fg=gray>make deploy</> to deploy application to target server.');

        return Command::SUCCESS;
    }

    private function askForHosting(InputInterface $input, OutputInterface $output): string
    {
        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion(
            'Select target hosting',
            $this->generatorRegistry->names(),
            'custom'
        );
        $question->setErrorMessage('Hosting %s is invalid.');

        return $helper->ask($input, $output, $question);
    }

    private function ask(string $question, InputInterface $input, OutputInterface $output): string
    {
        return $this->getHelper('question')->ask($input, $output, new Question($question.': '));
    }

    private function confirm(string $question, InputInterface $input, OutputInterface $output): bool
    {
        $question = sprintf('%s %s', $question, '<fg=cyan>[yes,no]</> ');

        return $this->getHelper('question')->ask($input, $output, new ConfirmationQuestion($question, false));
    }
}
