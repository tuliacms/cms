<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\UserInterface\Console\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Tulia\Cms\Platform\Application\UseCase\SetupSystem;
use Tulia\Cms\Platform\Application\UseCase\SetupSystemRequest;

/**
 * @author Adam Banaszkiewicz
 */
#[AsCommand(name: 'cms:setup')]
class Setup extends Command
{
    public function __construct(
        private readonly SetupSystem $setupSystem,
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Setups the new project.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->writeln(<<<EOF
<fg=#0088cc>
████████╗██╗   ██╗██╗     ██╗ █████╗      ██████╗███╗   ███╗███████╗
╚══██╔══╝██║   ██║██║     ██║██╔══██╗    ██╔════╝████╗ ████║██╔════╝
   ██║   ██║   ██║██║     ██║███████║    ██║     ██╔████╔██║███████╗
   ██║   ██║   ██║██║     ██║██╔══██║    ██║     ██║╚██╔╝██║╚════██║
   ██║   ╚██████╔╝███████╗██║██║  ██║    ╚██████╗██║ ╚═╝ ██║███████║
   ╚═╝    ╚═════╝ ╚══════╝╚═╝╚═╝  ╚═╝     ╚═════╝╚═╝     ╚═╝╚══════╝
</>
              Welcome in the World of Tulia CMS!

EOF
);

        $io->writeln('<comment>Now, answer some questions to initialize Your Tulia CMS installation.</comment>');

        $websiteName = $this->askFor('Website name', $input, $output);
        $websiteLocale = $this->askFor('Website default locale', $input, $output, default: 'en_US');
        $websiteDomainDevelopment = $this->askFor('Website local domain', $input, $output, default: 'localhost');
        $websiteDomain = $this->askFor('Website production domain (leave empty if not known yet)', $input, $output, default: 'localhost');

        $username = $this->askFor('Admin email', $input, $output, default: 'admin@gmail.com', validator: static function ($answer) {
            if (!filter_var($answer, FILTER_VALIDATE_EMAIL)) {
                throw new \RuntimeException('Please provide a valid e-mail address.');
            }
        });
        $password = $this->askFor('Admin password', $input, $output, default: 'admin', hidden: true);
        $sampleData = $this->askFor('I want to load sample website data', $input, $output, default: 'yes');

        $output->writeln('<info>Setting up...</info>');

        ($this->setupSystem)(new SetupSystemRequest(
            $websiteName,
            $websiteLocale,
            $websiteDomainDevelopment,
            $websiteDomain,
            $username,
            $password,
            $sampleData === 'yes',
        ));

        $io->writeln(<<<EOF
<fg=#00ff00>
███████╗██╗   ██╗ ██████╗ ██████╗███████╗███████╗███████╗
██╔════╝██║   ██║██╔════╝██╔════╝██╔════╝██╔════╝██╔════╝
███████╗██║   ██║██║     ██║     █████╗  ███████╗███████╗
╚════██║██║   ██║██║     ██║     ██╔══╝  ╚════██║╚════██║
███████║╚██████╔╝╚██████╗╚██████╗███████╗███████║███████║
╚══════╝ ╚═════╝  ╚═════╝ ╚═════╝╚══════╝╚══════╝╚══════╝
</>
    <info>Tulia CMS installed. Let's start a new adventure!</info>

EOF
        );

        $output->writeln('Frontend: http://localhost/');
        $output->writeln(sprintf('Backend:  http://localhost/administrator/ (<comment>%s</comment>|<comment>%s</comment>)', $username, $password));

        return Command::SUCCESS;
    }

    private function askFor(
        string $questionMessage,
        InputInterface $input,
        OutputInterface $output,
        callable $validator = null,
        mixed $default = '',
        bool $required = true,
        bool $hidden = false,
    ): string {
        if ($default) {
            $defaultValue = sprintf(' <fg=#eeeeee>(%s)</>', $default);
        } else {
            $defaultValue = '';
        }

        $question = new Question(sprintf('%s%s: ', $questionMessage, $defaultValue), $default);
        $question->setHidden($hidden);
        $question->setValidator(function ($answer) use ($questionMessage, $required, $validator) {
            if ($required && empty($answer)) {
                throw new \RuntimeException(sprintf('Please provide a %s.', strtolower($questionMessage)));
            }

            if (is_callable($validator)) {
                $validator($answer);
            }

            return $answer;
        });

        return $this->getHelper('question')->ask($input, $output, $question);
    }

    private function askForChoices(
        string $questionMessage,
        InputInterface $input,
        OutputInterface $output,
        array $choices
    ): string {
        $question = new ChoiceQuestion(
            sprintf('%s <fg=#eeeeee>(%s)</>: ', $questionMessage, $choices[0]),
            $choices,
            $choices[0]
        );
        $question->setErrorMessage('Domain %s is invalid.');

        return $this->getHelper('question')->ask($input, $output, $question);
    }
}
