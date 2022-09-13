<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\UserInterface\Console\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;
use Tulia\Cms\Theme\UserInterface\Console\Command\Traits\ThemeQuestionableTrait;
use Tulia\Component\Theme\ManagerInterface;

/**
 * @author Adam Banaszkiewicz
 */
#[AsCommand(name: 'make:theme:tulia-editor:init')]
final class MakeTuliaEditorInit extends Command
{
    use ThemeQuestionableTrait;

    public function __construct(
        private readonly ManagerInterface $themeManager,
        private readonly string $filesTemplates
    ) {
        parent::__construct(static::$defaultName);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $symfonyStyle = new SymfonyStyle($input, $output);
        $symfonyStyle->writeln('<info>You are going to initiate Tulia Editor package in theme. Answer some questions to continue.</info>');

        $theme = $this->askForTheme($input, $output);

        $replacements = $this->generateReplacements($theme->getName());
        $filesList = $this->collectFilesList('tulia-editor.init');
        $filesList = $this->interpolate($filesList, $replacements);

        $symfonyStyle->writeln('<fg=gray>Generating files...</>');
        $this->writeFilesToTheme($filesList, $theme->getDirectory().'/Resources/public/tulia-editor-blocks');
        $symfonyStyle->writeln('<fg=gray>Updating assets in config...</>');
        $this->updateAssets($theme->getDirectory(), $theme->getName());

        $symfonyStyle->writeln('<info>All done. Now go to directory, install dependencies and run. To do this call those commands:</info>');
        $symfonyStyle->writeln('  - <fg=bright-blue>>_ cd /YourTheme/Resources/public/tulia-editor-blocks</>');
        $symfonyStyle->writeln('  - <fg=bright-blue>>_ npm i</>');
        $symfonyStyle->writeln('  - <fg=bright-blue>>_ npm run watch</>');

        return Command::SUCCESS;
    }

    private function generateReplacements(string $theme): array
    {
        return [
            '{{ theme.public_path }}' => strtolower($theme),
            '{{ theme.name }}' => $theme,
        ];
    }

    private function collectFilesList(string $group): array
    {
        $finder = new Finder();
        $finder->files()->ignoreDotFiles(false)->in($this->filesTemplates.'/'.$group);

        $files = [];

        foreach ($finder as $file) {
            $files[$file->getRelativePathname()] = $file->getContents();
        }

        return $files;
    }

    private function interpolate(array $filesList, array $replacements): array
    {
        foreach ($filesList as $key => $file) {
            unset($filesList[$key]);
            $key = (string) str_replace(array_keys($replacements), array_values($replacements), $key);

            $filesList[$key] = str_replace(array_keys($replacements), array_values($replacements), $file);
        }

        return $filesList;
    }

    private function writeFilesToTheme(array $filesList, string $destination): void
    {
        $filesystem = new Filesystem();

        foreach ($filesList as $filename => $content) {
            $filepath = $destination.'/'.$filename;
            $directory = dirname($filepath);

            if (!is_dir($destination)) {
                $filesystem->mkdir($directory);
            }

            $filesystem->dumpFile($filepath, $content);
        }
    }

    private function updateAssets(string $themeRoot, string $themeName): void
    {
        $configFile = $themeRoot.'/Resources/config/config.yaml';

        $value = Yaml::parseFile($configFile);
        $assetKey = strtolower(str_replace('/', '.', $themeName)).'.editor.blocks';

        $value['cms']['assetter']['assets'][$assetKey] = [
            'scripts' => [
                '/assets/theme/'.strtolower($themeName).'/tulia-editor-blocks/theme-tulia-editor-blocks.js',
            ],
            'collection' => 'tulia_editor',
        ];

        (new Filesystem())->dumpFile($configFile, Yaml::dump($value, 20));
    }
}
