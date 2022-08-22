<?php

declare(strict_types=1);

namespace Tulia\Cms\Options\Domain\WriteModel;

use Tulia\Cms\Options\Domain\WriteModel\Model\Option;

/**
 * @author Adam Banaszkiewicz
 */
interface OptionsRepositoryInterface
{
    public function get(string $name, string $websiteId): Option;
    /**
     * @return Option[]
     */
    public function getAllForWebsite(string $websiteId): array;
    public function save(Option $option): void;
    public function delete(Option $option): void;
}
