<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Twig\Extension;

use Tulia\Component\Hooks\HooksInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
final class HooksExtension extends AbstractExtension
{
    public function __construct(
        private readonly HooksInterface $hooks,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('do_action', function (string $action, array $parameters = []) {
                return $this->hooks->doAction($action, ...$parameters);
            }, [
                'is_safe' => [ 'html' ]
            ]),
        ];
    }
}
