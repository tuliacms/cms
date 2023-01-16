<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Cms\Options;

use Tulia\Cms\Content\Type\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\Node\Domain\WriteModel\Service\NodeOptionsInterface;
use Tulia\Cms\Options\Domain\ReadModel\Options;

/**
 * @author Adam Banaszkiewicz
 */
final class OptionsAwareNodeOptions implements NodeOptionsInterface
{
    public function __construct(
        private readonly Options $options,
    ) {
    }

    public function get(
        string $name,
        ContentType|string $contentType,
        mixed $default = null,
        ?string $websiteId = null,
        ?string $locale = null,
    ): mixed {
        return $this->options->get(
            sprintf(
                'node.%s.category_taxonomy',
                $contentType instanceof ContentType ? $contentType->getCode() : $contentType
            ),
            $default,
            $websiteId,
            $locale,
        );
    }
}
