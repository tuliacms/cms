<?php

declare(strict_types=1);

namespace Tulia\Cms\Deployment\Infrastructure\Hosting\DeploymentFileGenerator;

/**
 * @author Adam Banaszkiewicz
 */
final class SmallPlFileGenerator extends AbstractDeploymentFileGenerator
{
    public function questions(): iterable
    {
        yield 'hosting.host' => $this->askFor('Server SSH host');
        yield 'hosting.user' => $this->askFor('Server SSH user');
        yield 'domain' => $this->askFor('Domain');
    }

    public function generate(array $parameters): string
    {
        return $this->interpolate(
            file_get_contents(\dirname(__DIR__, 2).'/Framework/Resources/recipes/small.pl.php'),
            $parameters
        );
    }

    public function supports(): string
    {
        return 'small.pl';
    }
}
