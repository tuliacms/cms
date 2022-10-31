<?php

declare(strict_types=1);

namespace Tulia\Cms\Security\Controller\Frontend;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Security\Framework\Security\Http\Csrf\Annotation\IgnoreCsrfToken;

/**
 * @author Adam Banaszkiewicz
 */
class Security extends AbstractController
{
    /**
     * @IgnoreCsrfToken
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Disable this functionality for now...
        return $this->redirectToRoute('frontend.homepage');

        if ($this->getUser()) {
            return $this->redirectToRoute('frontend.homepage');
        }

        return $this->render('@cms/security/login.tpl', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError()
        ]);
    }

    public function logout(): never
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
