<?php

declare(strict_types=1);

namespace Tulia\Cms\Tests\Behat\Content\Type\TestDoubles;

use Tulia\Cms\Content\Type\Domain\WriteModel\Service\ContentTypeExistanceDetectorInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class ContentTypeExistanceDetectorMock implements ContentTypeExistanceDetectorInterface
{
    public function __construct(
        private string $code,
        private bool $exists
    ) {
    }

    public function exists(string $code): bool
    {
        return $this->code === $code ? $this->exists : false;
    }
}
