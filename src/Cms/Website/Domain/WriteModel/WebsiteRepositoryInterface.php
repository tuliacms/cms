<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\WriteModel;

use Tulia\Cms\Website\Domain\WriteModel\Exception\WebsiteNotFoundException;
use Tulia\Cms\Website\Domain\WriteModel\Model\Website;

/**
 * @author Adam Banaszkiewicz
 */
interface WebsiteRepositoryInterface
{
    public function getNextId(): string;
    public function get(string $id): Website;
    public function save(Website $website): void;
    public function delete(Website $website): void;
}
