<?php

declare(strict_types=1);

namespace Tulia\Cms\Options\Domain\WriteModel\Service;

use Tulia\Cms\Options\Domain\WriteModel\Model\Option;
use Tulia\Cms\Options\Domain\WriteModel\Query\ExistingOptionsQueryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class MissingOptionsService
{
    public function __construct(
        private readonly ExistingOptionsQueryInterface $existingOptionsQuery,
        private readonly RegisteredOptionsRegistry $optionsRegistry,
    ) {
    }

    /**
     * @return Option[]
     */
    public function collectMissingOptionsForWebsite(string $websiteId): array
    {
        $options = $this->optionsRegistry->all();
        $existingOptions = $this->existingOptionsQuery->collectNames($websiteId);
        $result = [];

        foreach ($options as $name => $option) {
            if (in_array($name, $existingOptions, true)) {
                continue;
            }

            $result[] = new Option(
                $name,
                $option['value'],
                $websiteId,
                $option['multilingual'],
                $option['autoload']
            );
        }

        return $result;
    }
}
