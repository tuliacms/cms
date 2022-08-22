<?php

declare(strict_types=1);

namespace Tulia\Cms\Options\Infrastructure\Framework\Twig\Extension;

use Tulia\Cms\Options\Domain\ReadModel\Options;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class OptionsExtension extends AbstractExtension
{
    public function __construct(
        private readonly Options $options,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('option', function (string $name, $default = null) {
                return $this->options->get($name, default: $default);
            }, [
                'is_safe' => [ 'html' ]
            ]),
        ];
    }
}
