<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Infrastructure\Persistence\Doctrine\Dbal\Finder;

use Doctrine\DBAL\Connection;
use Tulia\Cms\Content\Attributes\Domain\ReadModel\Service\AttributesFinder;
use Tulia\Cms\Filemanager\Domain\ReadModel\Finder\FileFinderInterface;
use Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\AbstractFinder;
use Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\Query\QueryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalFinder extends AbstractFinder implements FileFinderInterface
{
    public function __construct(
        private Connection $connection,
        private AttributesFinder $metadataFinder
    ) {
    }

    public function getAlias(): string
    {
        return 'file';
    }

    public function createQuery(): QueryInterface
    {
        return new DbalQuery($this->connection->createQueryBuilder(), $this->metadataFinder);
    }
}
