<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\ReadModel;

use Tulia\Cms\Taxonomy\Domain\ReadModel\Model\Term;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Service\TaxonomyBreadcrumbsReadStorageInterface;

/**
 * @author Adam Banaszkiewicz
 */
class TaxonomyBreadcrumbs
{
    private TaxonomyBreadcrumbsReadStorageInterface $storage;

    public function __construct(TaxonomyBreadcrumbsReadStorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @return Term[]
     */
    public function find(string $termId): array
    {
        $source = $this->storage->find(
            $termId,
            $this->currentWebsite->getLocale()->getCode(),
            $this->currentWebsite->getDefaultLocale()->getCode(),
        );

        $result = [];

        foreach ($source as $row) {
            $result[] = Term::buildFromArray($row);
        }

        return $result;
    }
}
