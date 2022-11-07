<?php

declare(strict_types=1);

namespace Tulia\Cms\Seo\Domain\Service;

use Tulia\Cms\Content\Attributes\Domain\ReadModel\Model\AttributesAwareInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface SeoDocumentProcessorInterface
{
    public function aware(AttributesAwareInterface $document, ?string $title = null): void;
}
