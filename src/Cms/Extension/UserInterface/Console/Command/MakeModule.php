<?php

declare(strict_types=1);

namespace Tulia\Cms\Extension\UserInterface\Console\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Tulia\Cms\Extension\UserInterface\Console\Command\Traits\MakerFilesManagementTrait;
use Tulia\Cms\Extension\UserInterface\Console\Command\Traits\ThemeQuestionableTrait;
use Tulia\Cms\Platform\Infrastructure\Composer\Extensions\ExtensionSourceEnum;
use Tulia\Cms\Platform\Infrastructure\Composer\Extensions\ExtensionsStorage;
use Tulia\Component\Theme\ManagerInterface;

/**
 * @author Adam Banaszkiewicz
 */
#[AsCommand(name: 'make:module', description: 'Makes new local module')]
final class MakeModule extends Command
{
    use ThemeQuestionableTrait;
    use MakerFilesManagementTrait;

    public function __construct(
        private readonly ManagerInterface $themeManager,
        private readonly string $filesTemplates,
        private readonly string $modulesDir,
        private readonly ExtensionsStorage $extensionsStorage,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        $io = new SymfonyStyle($input, $output);

        $moduleCode = $this->askForModuleCode($input, $output);
        $moduleName = $helper->ask($input, $output, new Question('Module name: '));
        $moduleInfo = $helper->ask($input, $output, new Question('Short info: '));
        $replacements = $this->generateReplacements($moduleCode, $moduleName, $moduleInfo);
        $filesList = $this->collectFilesList($this->filesTemplates . '/module');
        $filesList = $this->interpolate($filesList, $replacements);

        $io->writeln('<comment>Generating module...</comment>');
        $this->writeFiles($filesList, $this->modulesDir . '/' . $moduleCode);
        $this->appendToComposerExtensions($moduleCode, $this->modulesDir . '/' . $moduleCode);

        $io->writeln('<comment>Updating composer.json (may take while)...</comment>');
        $this->requirePackageToComposer($moduleCode);

        $io->writeln('<info>Module generated.</info>');

        return Command::SUCCESS;
    }

    private function generateReplacements(string $moduleCode, string $moduleName, string $moduleInfo): array
    {
        $codeNamespaced = str_replace('/', '\\', $moduleCode);
        $codeClassname = preg_replace('#([^a-z0-9]+)#i', '', $moduleCode).'Module';

        return [
            '{{ module.code.lc }}' => strtolower($moduleCode),
            '{{ module.code.namespaced }}' => $codeNamespaced,
            '{{ module.code.namespaced.escaped }}' => str_replace('\\', '\\\\', $codeNamespaced),
            '{{ module.code.classname }}' => $codeClassname,
            '{{ module.code }}' => $moduleCode,
            '{{ module.name }}' => $moduleName,
            '{{ module.info }}' => $moduleInfo,
            '{{ module.translation_domain }}' => str_replace('/', '_', strtolower($moduleCode)),
            '{{ module.entrypoint.escaped }}' => str_replace('\\', '\\\\', $codeNamespaced).'\\\\'.$codeClassname,
            '{{ package.name }}' => $this->preparePackageName($moduleCode),
        ];
    }

    private function appendToComposerExtensions(string $moduleName, string $directory): void
    {
        $this->extensionsStorage->appendModule(
            $this->preparePackageName($moduleName),
            '0.0.1',
            ExtensionSourceEnum::LOCAL,
            $directory,
        );
        $this->extensionsStorage->write();
    }

    public function askForModuleCode(InputInterface $input, OutputInterface $output): string
    {
        $helper = $this->getHelper('question');
        $question = new Question('Module code (Vendor/ModuleName): ');
        $question->setValidator(function ($answer) {
            $answer = (string) $answer;
            if (!preg_match('#^([a-z0-9]+)/([a-z0-9]+)$#i', $answer)) {
                throw new \RuntimeException('Please provide module name in format: Vendor/ModuleName');
            }

            return $answer;
        });

        return $helper->ask($input, $output, $question);
    }

    private function preparePackageName(string $moduleCode): string
    {
        return ltrim(str_replace(
            ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '/-'],
            ['-a', '-b', '-c', '-d', '-e', '-f', '-g', '-h', '-i', '-j', '-k', '-l', '-m', '-n', '-o', '-p', '-q', '-r', '-s', '-t', '-u', '-v', '-w', '-x', '-y', '-z', '/'],
            $moduleCode
        ), '-');
    }

    private function requirePackageToComposer(string $moduleCode): void
    {
        $process = new Process(['composer', 'require', $this->preparePackageName($moduleCode)]);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }
}
