<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\WriteModel\Service;

use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Taxonomy;

/**
 * @author Adam Banaszkiewicz
 */
interface TaxonomyRepositoryInterface
{
    public function get(string $type, string $websiteId): Taxonomy;

    public function save(Taxonomy $taxonomy): void;

    public function delete (Taxonomy $taxonomy): void;

    public function getNextId(): string;
}
