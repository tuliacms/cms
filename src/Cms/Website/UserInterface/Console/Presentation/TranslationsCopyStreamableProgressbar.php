<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\UserInterface\Console\Presentation;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tulia\Cms\Website\Domain\WriteModel\Service\TranslationCopyMachine\ProgressStreamInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class TranslationsCopyStreamableProgressbar implements ProgressStreamInterface
{
    private ProgressBar $progressBar;

    public function __construct(
        private readonly InputInterface $input,
        private readonly OutputInterface $output,
    ) {
    }

    public function start(int $total): void
    {
        $this->progressBar = new ProgressBar($this->output, $total);
    }

    public function advance(int $done): void
    {
        $this->progressBar->advance($done);
    }

    public function end(): void
    {
        $this->progressBar->finish();
        $this->output->writeln('');
        $this->output->writeln('');
    }
}
