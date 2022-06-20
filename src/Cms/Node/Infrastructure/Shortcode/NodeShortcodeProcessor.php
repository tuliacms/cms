<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Shortcode;

use Tulia\Cms\Node\Domain\WriteModel\Service\ShortcodeProcessorInterface;
use Tulia\Component\Shortcode\ProcessorInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class NodeShortcodeProcessor implements ShortcodeProcessorInterface
{
    public function __construct(
        private ProcessorInterface $processor
    ) {
    }

    public function process(string $input): string
    {
        return $this->processor->process($input);
    }
}
