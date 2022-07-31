<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Domain\ReadModel\Query;

/**
 * @author Adam Banaszkiewicz
 */
interface UserSearchCollectorInterface
{
    public function collectDocuments(int $offset, int $limit): array;
    public function countDocuments(): int;
}
