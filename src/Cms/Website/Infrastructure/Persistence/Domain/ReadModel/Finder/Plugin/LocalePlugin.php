<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Infrastructure\Persistence\Domain\ReadModel\Finder\Plugin;

use Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\Plugin\AbstractDbalPlugin;

/**
 * @author Adam Banaszkiewicz
 */
class LocalePlugin extends AbstractDbalPlugin
{
    public function filterCriteria(array $criteria): array
    {
        //$criteria['locale'] = $this->currentWebsite->getLocale()->getCode();

        return $criteria;
    }
}
