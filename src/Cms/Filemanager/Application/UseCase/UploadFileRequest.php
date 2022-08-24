<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Application\UseCase;

use Tulia\Cms\Shared\Application\UseCase\RequestInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class UploadFileRequest implements RequestInterface
{
    public function __construct(
        public readonly string $directory,
        public readonly string $filepath,
        public readonly ?string $filename = null,
    ) {
    }
}
