<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as SymfonyController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Exception\NotDevelopmentEnvironmentAccessDeniedException;
use Tulia\Cms\Platform\Shared\Document\DocumentInterface;
use Tulia\Cms\Shared\Domain\WriteModel\UuidGeneratorInterface;
use Tulia\Component\Templating\View;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractController extends SymfonyController
{
    public static function getSubscribedServices(): array
    {
        return parent::getSubscribedServices() + [
            RequestStack::class,
            TranslatorInterface::class,
            DocumentInterface::class,
        ];
    }

    public function getFlashes(): array
    {
        /** @var Request $request */
        $request = $this->container->get(RequestStack::class)->getMainRequest();

        if ($request) {
            return $request->getSession()->getFlashBag()->all();
        }

        return [];
    }

    /**
     * @param string|array $views
     * @param array $data
     *
     * @return ViewInterface
     */
    public function view($views, array $data = []): ViewInterface
    {
        return new View($views, $data);
    }

    public function trans(?string $id, array $parameters = [], string $domain = null, string $locale = null): ?string
    {
        return $this->container->get(TranslatorInterface::class)->trans($id, $parameters, $domain, $locale);
    }

    public function responseJson(array $data = null, int $status = 200, array $headers = [], bool $json = false): JsonResponse
    {
        return new JsonResponse($data, $status, $headers, $json);
    }

    public function uuid(): string
    {
        return $this->container->get(UuidGeneratorInterface::class)->generate();
    }

    public function getDocument(): DocumentInterface
    {
        return $this->container->get(DocumentInterface::class);
    }

    public function isLoggedIn(): bool
    {
        return $this->isGranted('IS_AUTHENTICATED_FULLY');
    }

    public function isHomepage(Request $request): bool
    {
        return $request->getPathInfo() === $this->generateUrl('frontend.homepage');
    }

    /**
     * @throws NotDevelopmentEnvironmentAccessDeniedException
     */
    public function denyIfNotDevelopmentEnvironment(): void
    {
        if ($this->getParameter('kernel.environment') !== 'dev') {
            throw new NotDevelopmentEnvironmentAccessDeniedException('Access denied. Cannot do that in not development environment.');
        }
    }
}
