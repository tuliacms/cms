<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\ReadModel\Service;

use Tulia\Cms\Content\Type\Domain\ReadModel\ContentTypeBuilder\ContentTypeCollector;

/**
 * @author Adam Banaszkiewicz
 */
interface ContentTypeDecoratorInterface
{
    public function decorate(ContentTypeCollector $collector): void;
}
