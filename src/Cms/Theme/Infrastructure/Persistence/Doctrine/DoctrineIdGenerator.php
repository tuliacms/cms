<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Infrastructure\Persistence\Doctrine;

use Symfony\Component\Uid\Uuid;
use Tulia\Cms\Theme\Domain\WriteModel\Service\IdGeneratorInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class DoctrineIdGenerator implements IdGeneratorInterface
{
    public function getNextId(): string
    {
        return (string) Uuid::v4();
    }
}
