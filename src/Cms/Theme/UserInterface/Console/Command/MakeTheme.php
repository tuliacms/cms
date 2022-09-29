<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\UserInterface\Console\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Tulia\Cms\Theme\UserInterface\Console\Command\Traits\MakerFilesManagementTrait;
use Tulia\Cms\Theme\UserInterface\Console\Command\Traits\ThemeQuestionableTrait;
use Tulia\Component\Theme\ManagerInterface;
use Tulia\Component\Theme\ThemeInterface;

/**
 * @author Adam Banaszkiewicz
 */
#[AsCommand(name: 'make:theme')]
final class MakeTheme extends Command
{
    use ThemeQuestionableTrait;
    use MakerFilesManagementTrait;

    public function __construct(
        private readonly ManagerInterface $themeManager,
        private readonly string $filesTemplates,
        private readonly string $themesDir,
    ){
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $generateChild = $this->askForThemeType($input, $output);

        if ($generateChild) {
            $parentTheme = $this->askForTheme($input, $output);
        } else {
            $parentTheme = null;
        }

        if ($parentTheme) {
            if ($parentTheme->hasParent()) {
                $io->error(sprintf('Theme %s has already parent (%s), cannot stack parents.', $parentTheme->getName(), $parentTheme->getParentName()));
                return Command::INVALID;
            }
        }

        $themeName = $this->askForThemeName($input, $output);
        $replacements = $this->generateReplacements($themeName, $parentTheme);
        $filesList = $this->collectFilesList($this->filesTemplates.'/theme');
        $filesList = $this->interpolate($filesList, $replacements);

        $io->writeln('<fg=gray>Generating files...</>');
        $this->writeFiles($filesList, $this->themesDir.'/'.$themeName);

        $io->writeln('<info>Theme generated. Next steps:</info>');
        $io->writeln('   1. Activate Your theme in Administration Panel');
        $io->writeln(sprintf('   2. Go to theme webpack directory: <fg=gray>cd extension/theme/%s/Resources/public/theme</>', $themeName));
        $io->writeln('   3. Install dependencies of Webpack: <fg=gray>npm install</>');
        $io->writeln('   4. Start developing: <fg=gray>npm run watch</>');
        $io->writeln('');
        $io->writeln('<info>When You will be ready to develop blocks for Tulia Editor:</info>');
        $io->writeln(sprintf('   1. Go to theme webpack directory: <fg=gray>cd extension/theme/%s/Resources/public/tulia-editor-blocks</>', $themeName));
        $io->writeln('   2. Install dependencies of Webpack: <fg=gray>npm install</>');
        $io->writeln('   3. Start developing: <fg=gray>npm run watch</>');

        return Command::SUCCESS;
    }

    private function generateReplacements(string $themeName, ?ThemeInterface $parent): array
    {
        [$vendor, $code] = explode('/', $themeName);

        return [
            '{{ theme.name.lc }}' => strtolower($themeName),
            '{{ theme.name }}' => $themeName,
            '{{ theme.translation_domain }}' => str_replace('/', '_', strtolower($themeName)),
            '{{ theme.assets_prefix }}' => 'theme.'.strtolower($vendor).'.'.strtolower($code),
            '{{ theme.parent }}' => $parent ? "'".$parent->getName()."'" : 'null',
            '{{ theme.vendor }}' => $vendor,
            '{{ theme.code }}' => $code,
            '{{ theme.vendor.lc }}' => strtolower($vendor),
            '{{ theme.code.lc }}' => strtolower($code),
        ];
    }
}
