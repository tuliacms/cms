<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Twig\Extension;

use Tulia\Cms\Shared\Infrastructure\Utils\DateTime\DateFormatterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class DatetimeExtension extends AbstractExtension
{
    protected DateFormatterInterface $formatter;

    public function __construct(DateFormatterInterface $formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('format_date', function ($date = 'now', string $format = null) {
                return $this->formatter->format($date, $format);
            }, [
                'is_safe' => [ 'html' ]
            ]),
        ];
    }
}
