<?php

declare(strict_types=1);

namespace Tulia\Cms\Options\Infrastructure\Framework\Config;

use Tulia\Cms\Options\Domain\WriteModel\Service\OptionsProviderInterface;

/**
 * @author Adam Banaszkiewicz
 * @fonal
 * @lazy
 */
class YamlOptionsProvider implements OptionsProviderInterface
{
    public function __construct(
        private readonly array $definitions,
    ) {
    }

    public function provide(): array
    {
        return $this->definitions;
    }
}
