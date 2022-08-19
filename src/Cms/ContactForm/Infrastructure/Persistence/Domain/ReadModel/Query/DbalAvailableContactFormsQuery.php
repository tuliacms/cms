<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Infrastructure\Persistence\Domain\ReadModel\Query;

use Doctrine\DBAL\Connection;
use Tulia\Cms\ContactForm\Domain\ReadModel\Query\AvailableContactFormsQueryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class DbalAvailableContactFormsQuery implements AvailableContactFormsQueryInterface
{
    public function __construct(
        private readonly Connection $connection
    ) {
    }

    public function list(): array
    {
        return $this->connection->fetchAllKeyValue('SELECT BIN_TO_UUID(id), name FROM #__form ORDER BY name ASC');
    }
}
