<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Twig\Extension;

use Symfony\Component\Uid\Uuid;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;

/**
 * @author Adam Banaszkiewicz
 */
class UtilsExtension extends AbstractExtension
{
    public function __construct(
        private readonly string $environment,
    ) {
    }

    public function getTests(): array
    {
        return [
            new TwigTest('uuid', static function($var) {
                if (
                    $var === null
                    || is_scalar($var)
                    || (is_object($var) && method_exists($var, '__toString'))
                ) {
                    return Uuid::isValid((string) $var);
                }

                return false;
            }),
        ];
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
