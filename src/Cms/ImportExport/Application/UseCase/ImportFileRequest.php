<?php

declare(strict_types=1);

namespace Tulia\Cms\ImportExport\Application\UseCase;

use Tulia\Cms\Shared\Application\UseCase\RequestInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class ImportFileRequest implements RequestInterface
{
    public function __construct(
        public readonly string $filepath,
        public readonly string $websiteId,
        public readonly string $authorId,
        public readonly ?string $originalName = null,
    ) {
    }
}
