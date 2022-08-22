<?php

declare(strict_types=1);

namespace Tulia\Cms\Options\Application\UseCase;

use Tulia\Cms\Shared\Application\UseCase\RequestInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class RegisterMissingOptionsRequest implements RequestInterface
{
    public function __construct(
        public readonly array $websiteIdList
    ) {
    }
}
