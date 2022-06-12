<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\WriteModel\Service;

/**
 * @author Adam Banaszkiewicz
 */
final class ContentTypeExistanceDetector implements ContentTypeExistanceDetectorInterface
{
    public function __construct(
        private Configuration $config
    ) {
    }

    public function exists(string $code): bool
    {
        return \in_array($code, $this->config->getTypes(), true);
    }
}
