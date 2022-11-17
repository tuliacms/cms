<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\ReadModel\Service;

use Tulia\Cms\Content\Type\Domain\ReadModel\ContentTypeBuilder\ContentTypeCollector;

/**
 * @author Adam Banaszkiewicz
 */
interface ContentTypeProviderInterface
{
    public function provide(ContentTypeCollector $collector): void;
}
