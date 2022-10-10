<?php

declare(strict_types=1);

namespace Tulia\Cms\Deployment\Domain\WriteModel\Service;

/**
 * @author Adam Banaszkiewicz
 */
interface DeploymentFileGeneratorInterface
{
    public function questions(): iterable;

    public function generate(array $parameters): string;

    public function supports(): string;
}
