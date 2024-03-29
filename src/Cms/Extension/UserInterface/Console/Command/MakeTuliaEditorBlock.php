<?php

declare(strict_types=1);

namespace Tulia\Cms\Extension\UserInterface\Console\Command;

use Imagick;
use ImagickDraw;
use ImagickPixel;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Tulia\Cms\Extension\UserInterface\Console\Command\Traits\MakerFilesManagementTrait;
use Tulia\Cms\Extension\UserInterface\Console\Command\Traits\ThemeQuestionableTrait;
use Tulia\Component\Theme\ManagerInterface;

/**
 * @author Adam Banaszkiewicz
 */
#[AsCommand(name: 'make:theme:tulia-editor:block')]
final class MakeTuliaEditorBlock extends Command
{
    use ThemeQuestionableTrait;
    use MakerFilesManagementTrait;

    public function __construct(
        private readonly ManagerInterface $themeManager,
        private readonly string $filesTemplates,
        private readonly string $fontsPath,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $symfonyStyle = new SymfonyStyle($input, $output);
        $symfonyStyle->writeln(
            '<info>You are going to create new block for Tulia Editor in theme. Answer some questions to continue.</info>'
        );

        $theme = $this->askForTheme($input, $output);
        $blockName = $this->askForBlockName($input, $output);

        $replacements = $this->generateReplacements($theme->getName(), $blockName);
        $filesList = $this->collectFilesList($this->filesTemplates.'/tulia-editor.block');
        $filesList = $this->interpolate($filesList, $replacements);

        $symfonyStyle->writeln('<fg=gray>Generating files...</>');
        $this->writeFiles($filesList, $theme->getDirectory().'/Resources/public/tulia-editor-blocks/src');
        $this->updateBlockList($theme->getDirectory().'/Resources/public/tulia-editor-blocks/src', $blockName);

        $symfonyStyle->writeln('<info>All done.</info>');

        return Command::SUCCESS;
    }

    private function generateReplacements(string $theme, string $blockName): array
    {
        return [
            '{{ block.name }}' => $blockName,
            '{{ theme.name }}' => $theme,
            '{{ block.thumbnail }}' => $this->generateBlockThumbnail($blockName),
        ];
    }

    private function askForBlockName(InputInterface $input, OutputInterface $output): string
    {
        $question = new Question('Block name <fg=gray>(only alphanum, no spaces and other characters)</>: ');
        $question->setValidator(static function ($answer) {
            if (!is_string($answer) || !preg_match('/^[a-z0-9]+$/i', $answer)) {
                throw new \RuntimeException('Block must contain only alphanum characters.');
            }

            return $answer;
        });

        return $this->getHelper('question')->ask($input, $output, $question);
    }

    private function updateBlockList(string $destination, string $blockName): void
    {
        $filepath = $destination.'/index.js';

        $content = file_get_contents($filepath);
        $content .= "\nTuliaEditor.block(require('./{$blockName}/{$blockName}.js').default);";

        (new Filesystem())->dumpFile($filepath, $content);
    }

    private function generateBlockThumbnail(string $blockName): string
    {
        $width = 250;
        $height = 100;

        $image = new Imagick();
        $image->newImage($width, $height, new ImagickPixel('white'), 'png');
        $image->setImageFormat('png');

        $ctx = new ImagickDraw();
        $ctx->setFillColor(new ImagickPixel('black'));
        $ctx->setFont($this->fontsPath.'/Poppins-Regular.ttf');
        $ctx->setFontSize(20);

        $metrics = $image->queryFontMetrics($ctx, $blockName);

        $offset = [
            'x' => ($width / 2) - ($metrics['textWidth'] / 2),
            'y' => ($height / 2) + ($metrics['textHeight'] / 2),
        ];
        $image->annotateImage($ctx, $offset['x'], $offset['y'], 0, $blockName);

        return 'data:image/png;base64,' . base64_encode((string) $image);
    }
}
