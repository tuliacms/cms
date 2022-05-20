<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\ContentType\Domain\ReadModel\Service;

use Tulia\Cms\ContentBuilder\ContentType\Domain\ReadModel\Model\ContentType;

/**
 * @author Adam Banaszkiewicz
 */
interface ContentTypeDecoratorInterface
{
    public function decorate(ContentType $contentType): void;
}
