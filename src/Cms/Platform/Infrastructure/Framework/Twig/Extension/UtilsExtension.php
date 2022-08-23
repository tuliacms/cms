<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class UtilsExtension extends AbstractExtension
{
    public function __construct(
        private readonly string $environment,
    ) {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('shorten_uuid', function (string $uuid) {
                $segments = explode('-', $uuid);

                return $segments[0] ?? null;
            }, [
                'is_safe' => [ 'html' ]
            ]),
            new TwigFilter('md5', function ($input) {
                return md5($input);
            }, [
                'is_safe' => [ 'html' ]
            ]),
            new TwigFilter('uniqid', function (string $prefix = null) {
                return uniqid($prefix, true);
            }, [
                'is_safe' => [ 'html' ]
            ]),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_dev_env', function () {
                return $this->environment === 'dev';
            }, [
                'is_safe' => [ 'html' ]
            ]),
        ];
    }
}
