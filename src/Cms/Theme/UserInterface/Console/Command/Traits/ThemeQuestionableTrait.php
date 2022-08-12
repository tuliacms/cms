<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\UserInterface\Console\Command\Traits;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Tulia\Component\Theme\ThemeInterface;

/**
 * @author Adam Banaszkiewicz
 */
trait ThemeQuestionableTrait
{
    public function askForTheme(InputInterface $input, OutputInterface $output): ThemeInterface
    {
        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion(
            'Select theme',
            array_map(static fn($v) => $v->getName(), $this->themeManager->getThemes())
        );
        $question->setErrorMessage('Theme %s not exists.');
        $themeName = $helper->ask($input, $output, $question);

        return $this->themeManager->getThemes()[$themeName];
    }
}
