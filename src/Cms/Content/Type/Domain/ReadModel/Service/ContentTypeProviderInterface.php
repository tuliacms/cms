<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\ReadModel\Service;

use Tulia\Cms\Content\Type\Domain\ReadModel\Model\ContentType;

/**
 * @author Adam Banaszkiewicz
 */
interface ContentTypeProviderInterface
{
    /**
     * @return ContentType[]
     */
    public function provide(): array;
}
