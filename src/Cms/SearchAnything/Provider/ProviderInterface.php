<?php

declare(strict_types=1);

namespace Tulia\Cms\SearchAnything\Provider;

use Tulia\Cms\SearchAnything\Model\Results;

/**
 * @author Adam Banaszkiewicz
 */
interface ProviderInterface
{
    public function search(string $query, int $limit = 5, int $page = 1): Results;

    public function getId(): string;

    public function getLabel(): array;

    public function getIcon(): string;
}
