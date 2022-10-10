<?php

declare(strict_types=1);

namespace Tulia\Cms\Deployment\Domain\WriteModel\Service;

/**
 * @author Adam Banaszkiewicz
 * @final
 */
class DeploymentFileGeneratorRegistry
{
    /** @param DeploymentFileGeneratorInterface[] $generators */
    public function __construct(
        private readonly iterable $generators,
    ) {
    }

    public function all(): iterable
    {
        return $this->generators;
    }

    public function get(string $name): DeploymentFileGeneratorInterface
    {
        foreach ($this->generators as $generator) {
            if ($generator->supports() === $name) {
                return $generator;
            }
        }

        throw new \Exception(sprintf('Generator for "%s" does not exists.', $name));
    }

    /**
     * @return string[]
     */
    public function names(): array
    {
        return array_map(static fn ($v) => $v->supports(), $this->generators);
    }
}
