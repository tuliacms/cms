<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Application\UseCase;

use Tulia\Cms\Shared\Application\UseCase\RequestInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class CreateNodeRequest implements RequestInterface
{
    public function __construct(
        public readonly string $nodeType,
        public readonly string $author,
        public readonly array $details,
        public readonly array $attributes
    ) {
    }
}
