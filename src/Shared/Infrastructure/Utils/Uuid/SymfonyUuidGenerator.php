<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Infrastructure\Utils\Uuid;

use Symfony\Component\Uid\Uuid;
use Tulia\Cms\Shared\Domain\WriteModel\UuidGeneratorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class SymfonyUuidGenerator implements UuidGeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function generate(): string
    {
        return (string) Uuid::v4();
    }
}
