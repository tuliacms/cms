<?php

declare(strict_types=1);

namespace Tulia\Cms\Security\Controller\Backend;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteInterface;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Security extends AbstractController
{
    /**
     * @return ViewInterface|RedirectResponse
     */
    public function login(
        Request $request,
        AuthenticationUtils $authenticationUtils,
        WebsiteInterface $website,
    ) {
        if ($this->getUser()) {
            return $this->redirectToRoute('backend.homepage');
        }

        $locale = $website->getLocale()->getCode();

        $prefLocales = array_reduce(
            explode(',', str_replace('-', '_', $request->headers->get('Accept-Language'))),
            function ($res, $el) {
                list($l, $q) = array_merge(explode(';q=', $el), [1]);
                $res[$l] = (float) $q;
                return $res;
            },
            []
        );
        arsort($prefLocales);

        return $this->view('@backend/security/login.tpl', [
            'bgImages'      => $this->getCollection(),
            'last_username' => $request->query->get('username', $authenticationUtils->getLastUsername()),
            'password'      => $request->query->get('password'),
            'error'         => $authenticationUtils->getLastAuthenticationError(),
            'clientLocale'  => $prefLocales,
        ]);
    }

    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    public function getCollection(): array
    {
        $filepath = $this->getParameter('kernel.public_dir').'/assets/core/backend/theme/images/login-bg/collection.json';

        return json_decode(file_get_contents($filepath));
    }
}
