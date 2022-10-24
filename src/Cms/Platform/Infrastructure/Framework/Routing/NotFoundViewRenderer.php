<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Routing;

/**
 * @author Adam Banaszkiewicz
 */
final class NotFoundViewRenderer
{
    public function render(): never
    {
        header('HTTP/1.1 404 Not Found', true, 404);

        include dirname(__DIR__).'/Resources/views/404.php';

        exit;
    }
}
