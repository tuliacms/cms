<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\UserInterface\Console\Command\Traits;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
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

    public function askForThemeType(InputInterface $input, OutputInterface $output): bool
    {
        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion(
            'You want to generate a child theme?',
            ['yes', 'no']
        );
        $question->setErrorMessage('Theme %s not exists.');
        $question->setAutocompleterValues(['yes', 'no']);

        return $helper->ask($input, $output, $question) === 'yes';
    }

    public function askForThemeName(InputInterface $input, OutputInterface $output): string
    {
        $helper = $this->getHelper('question');
        $question = new Question('Theme name: ');
        $question->setValidator(function ($answer) {
            $answer = (string) $answer;
            if (!preg_match('#^([a-z0-9]+)/([a-z0-9]+)$#i', $answer)) {
                throw new \RuntimeException('Please provide theme name in format: Vendor/Name');
            }

            if (isset($this->themeManager->getThemes()[$answer])) {
                throw new \RuntimeException('This theme already exists in system, cannot create new one with this name.');
            }

            return $answer;
        });

        return $helper->ask($input, $output, $question);
    }
}
