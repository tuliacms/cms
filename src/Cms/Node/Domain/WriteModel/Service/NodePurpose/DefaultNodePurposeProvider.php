<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Service\NodePurpose;

use Tulia\Cms\Node\Domain\WriteModel\Model\Enum\NodePurposeEnum;

/**
 * @author Adam Banaszkiewicz
 */
class DefaultNodePurposeProvider implements NodePurposeProviderInterface
{
    public function provide(): array
    {
        return [
            NodePurposeEnum::PAGE_HOMEPAGE => [
                'singular' => true,
            ],
            NodePurposeEnum::PAGE_CONTACT => [
                'singular' => true,
            ],
            NodePurposeEnum::PAGE_PRIVACY_POLICY => [
                'singular' => true,
            ],
        ];
    }
}
