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
        private readonly \Traversable $generators,
    ) {
    }

    public function all(): \Traversable
    {
        return $this->generators;
    }

    public function get(string $name): DeploymentFileGeneratorInterface
    {
        foreach (iterator_to_array($this->generators) as $generator) {
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
        return array_map(static fn ($v) => $v->supports(), iterator_to_array($this->generators));
    }
}
