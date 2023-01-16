<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Service;

use Tulia\Cms\Content\Type\Domain\ReadModel\Model\ContentType;

/**
 * @author Adam Banaszkiewicz
 */
interface NodeOptionsInterface
{
    public function get(
        string $name,
        ContentType|string $contentType,
        mixed $default = null,
        ?string $websiteId = null,
        ?string $locale = null,
    ): mixed;
}
