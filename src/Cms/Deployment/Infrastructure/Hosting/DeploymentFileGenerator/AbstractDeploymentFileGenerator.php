<?php

declare(strict_types=1);

namespace Tulia\Cms\Deployment\Infrastructure\Hosting\DeploymentFileGenerator;

use Symfony\Component\Console\Question\Question;
use Tulia\Cms\Deployment\Domain\WriteModel\Service\DeploymentFileGeneratorInterface;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractDeploymentFileGenerator implements DeploymentFileGeneratorInterface
{
    abstract public function questions(): iterable;
    abstract public function generate(array $parameters): string;
    abstract public function supports(): string;

    protected function interpolate(string $pattern, array $replacements): string
    {
        return str_replace(array_keys($replacements), array_values($replacements), $pattern);
    }

    protected function askFor(string $question): Question
    {
        return new Question($question.': ');
    }
}
