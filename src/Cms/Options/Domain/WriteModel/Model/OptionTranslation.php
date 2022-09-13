<?php

declare(strict_types=1);

namespace Tulia\Cms\Options\Domain\WriteModel\Model;

/**
 * @author Adam Banaszkiewicz
 * @final
 */
class OptionTranslation
{
    private string $id;

    public function __construct(
        private Option $option,
        public string $locale,
        public mixed $value,
    ) {
    }
}
