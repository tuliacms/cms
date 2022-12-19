<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\UserInterface\Web\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteInterface;
use Tulia\Component\Templating\EngineInterface;
use Tulia\Component\Templating\Exception\ViewNotFoundException;
use Tulia\Component\Templating\View;
use Tulia\Component\Theme\ManagerInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class ErrorController extends AbstractController
{
    public function __construct(
        private readonly string $environment,
        private readonly EngineInterface $engine,
        private readonly ManagerInterface $manager,
    ) {
    }

    public function handle(\Throwable $exception, LoggerInterface $appLogger, WebsiteInterface $website)
    {
        $allowOriginalException = false;

        try {
            if (
                $exception instanceof NotFoundHttpException
                || ($exception instanceof HttpException && $exception->getStatusCode() >= 400 && $exception->getStatusCode() <= 499)
            ) {
                $code = Response::HTTP_NOT_FOUND;
            } else {
                $code = Response::HTTP_INTERNAL_SERVER_ERROR;
            }

            if ($this->environment !== 'prod') {
                $allowOriginalException = true;
                throw $exception;
            }

            if ($website->isBackend()) {
                $view = "@backend/error-{$code}.tpl";
            } else {
                $view = $this->manager->getTheme()->getViewsDirectory()."/{$code}.tpl";
            }

            return new Response($this->engine->render(new View($view)), $code);
        } catch (\Throwable $e) {
            if ($this->environment === 'prod') {
                return $this->createProduction500Error();
            }

            if ($allowOriginalException) {
                throw $e;
            }

            if ($e instanceof ViewNotFoundException) {
                throw new \Exception('Cannot render error page. Maybe You forgot to implement the 404.tpl view in Your theme?', 0, $e);
            }

            throw new \Exception('Cannot render error page. Reason of this error You can find below, in previous exception.', 0, $e);
        }
    }

    private function createProduction500Error(): Response
    {
        return new Response(
            file_get_contents(__DIR__.'/../../../Infrastructure/Framework/Resources/views/backend/error-500.prod.tpl'),
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
