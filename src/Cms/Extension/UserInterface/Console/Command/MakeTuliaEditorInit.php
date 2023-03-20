<?php

declare(strict_types=1);

namespace Tulia\Cms\Extension\UserInterface\Console\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;
use Tulia\Cms\Extension\UserInterface\Console\Command\Traits\MakerFilesManagementTrait;
use Tulia\Cms\Extension\UserInterface\Console\Command\Traits\ThemeQuestionableTrait;
use Tulia\Component\Theme\ManagerInterface;

/**
 * @author Adam Banaszkiewicz
 */
#[AsCommand(name: 'make:theme:tulia-editor:init')]
final class MakeTuliaEditorInit extends Command
{
    use ThemeQuestionableTrait;
    use MakerFilesManagementTrait;

    public function __construct(
        private readonly ManagerInterface $themeManager,
        private readonly string $filesTemplates
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $symfonyStyle = new SymfonyStyle($input, $output);
        $symfonyStyle->writeln('<info>You are going to initiate Tulia Editor package in theme. Answer some questions to continue.</info>');

        $theme = $this->askForTheme($input, $output);

        $replacements = $this->generateReplacements($theme->getName());
        $filesList = $this->collectFilesList($this->filesTemplates.'/tulia-editor.init');
        $filesList = $this->interpolate($filesList, $replacements);

        $symfonyStyle->writeln('<fg=gray>Generating files...</>');
        $this->writeFiles($filesList, $theme->getDirectory().'/Resources/public/tulia-editor-blocks');
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
