<?php

declare(strict_types=1);

namespace Tulia\Cms\Security\Controller\Backend;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Component\Routing\Website\WebsiteInterface;
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
        WebsiteInterface $website
    ) {
        if ($this->getUser()) {
            return $this->redirectToRoute('backend.homepage');
        }

        $users = [
            'en_US' => [
                'username' => 'admin@gmail.com',
                'password' => 'admin',
            ],
            'pl_PL' => [
                'username' => 'admin_pl@gmail.com',
                'password' => 'admin',
            ],
        ];
        $locale = $website->getLocale()->getCode();

        return $this->view('@backend/security/login.tpl', [
            'bgImages'      => $this->getCollection(),
            'last_username' => $request->query->get('username', $authenticationUtils->getLastUsername()),
            'error'         => $authenticationUtils->getLastAuthenticationError(),
            'username' => $users[$locale]['username'] ?? '',
            'password' => $users[$locale]['password'] ?? '',
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
