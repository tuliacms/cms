<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\UserInterface\Web\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Component\Routing\Website\WebsiteInterface;
use Tulia\Component\Templating\EngineInterface;
use Tulia\Component\Templating\View;

/**
 * @author Adam Banaszkiewicz
 */
final class ErrorController extends AbstractController
{
    public function __construct(
        private readonly string $environment,
        private readonly EngineInterface $engine,
    ) {
    }

    public function handle(\Throwable $exception, DebugLoggerInterface $logger, WebsiteInterface $website)
    {
        try {
            if ($exception instanceof NotFoundHttpException) {
                if ($website->isBackend()) {
                    $view = '@backend/error-404.tpl';
                    $code = Response::HTTP_NOT_FOUND;
                } else {
                    $view = '@theme/404.tpl';
                    $code = Response::HTTP_NOT_FOUND;
                }

                return new Response($this->engine->render(new View($view)), $code);
            }
        } catch (\Throwable $e) {
            if ($this->environment === 'prod') {
                return $this->createProduction500Error();
            }

            throw new \Exception('Cannot render error page. Reason of this error You can find below, in previous exception.', 0, $e);
        }

        if ($this->environment === 'prod') {
            return $this->createProduction500Error();
        }

        throw $exception;
    }

    private function createProduction500Error(): Response
    {
        return new Response(
            file_get_contents(__DIR__.'/../../../Infrastructure/Framework/Resources/views/backend/error-500.prod.tpl'),
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
