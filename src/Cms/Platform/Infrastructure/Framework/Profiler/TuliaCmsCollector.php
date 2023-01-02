<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Profiler;

use Symfony\Bundle\FrameworkBundle\DataCollector\AbstractDataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tulia\Cms\Platform\Version;

/**
 * @author Adam Banaszkiewicz
 */
final class TuliaCmsCollector extends AbstractDataCollector
{
    public function collect(Request $request, Response $response, \Throwable $exception = null)
    {
        $this->data = [
            'version' => Version::VERSION,
            'release_date' => Version::RELEASED,
        ];
    }

    public static function getTemplate(): ?string
    {
        return '@backend/profiler/tuliacms_profiler.html.twig';
    }

    public function getVersion(): string
    {
        return $this->data['version'];
    }

    public function getReleaseDate(): string
    {
        return $this->data['release_date'];
    }
}
