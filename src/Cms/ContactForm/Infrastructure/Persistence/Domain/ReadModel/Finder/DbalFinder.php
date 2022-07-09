<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Infrastructure\Persistence\Domain\ReadModel\Finder;

use Doctrine\DBAL\Connection;
use Tulia\Cms\ContactForm\Domain\ReadModel\Finder\ContactFormFinderInterface;
use Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\AbstractFinder;
use Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\Query\QueryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalFinder extends AbstractFinder implements ContactFormFinderInterface
{
    public function __construct(
        private Connection $connection
    ) {
    }

    public function getAlias(): string
    {
        return 'contact_form';
    }

    public function createQuery(): QueryInterface
    {
        return new DbalQuery($this->connection->createQueryBuilder());
    }
}
