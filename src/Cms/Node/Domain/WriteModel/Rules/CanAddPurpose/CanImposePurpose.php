<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Rules\CanAddPurpose;

use Tulia\Cms\Node\Domain\WriteModel\NewModel\Purpose;
use Tulia\Cms\Node\Domain\WriteModel\Service\NodeByPurposeFinderInterface;
use Tulia\Cms\Node\Domain\WriteModel\Service\NodePurpose\NodePurposeRegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class CanImposePurpose implements CanImposePurposeInterface
{
    public function __construct(
        private NodePurposeRegistryInterface $nodePurposeRegistry,
        private NodeByPurposeFinderInterface $nodeByFlagFinder,
    ) {
    }

    public function decide(
        string $nodeId,
        Purpose $purpose,
        Purpose ...$purposes
    ): CanImposePurposeReasonEnum {
        if ($this->purposeIsAlreadyImposed($purpose, $purposes)) {
            return CanImposePurposeReasonEnum::OK;
        }

        if ($this->purposeDoesntExists($purpose)) {
            return CanImposePurposeReasonEnum::PurposeDoesntExists;
        }

        if ($this->singularPurposeIsAlreadyAddedToAnotherNode($purpose, $nodeId)) {
            return CanImposePurposeReasonEnum::ThisSingularPurposeIsImposedToAnotherNode;
        }

        return CanImposePurposeReasonEnum::OK;
    }

    private function purposeDoesntExists(Purpose $purpose): bool
    {
        return false === $this->nodePurposeRegistry->has((string) $purpose);
    }

    private function singularPurposeIsAlreadyAddedToAnotherNode(Purpose $purpose, string $nodeId): bool
    {
        if ($this->nodePurposeRegistry->isSingular((string) $purpose) === false) {
            return false;
        }

        return 0 !== $this->nodeByFlagFinder->countOtherNodesWithPurpose(
            $nodeId,
            (string) $purpose
        );
    }

    private function purposeIsAlreadyImposed(Purpose $purpose, array $purposes): bool
    {
        return \in_array($purpose, $purposes, true);
    }
}
