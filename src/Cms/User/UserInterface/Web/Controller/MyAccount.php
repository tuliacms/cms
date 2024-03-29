<?php

declare(strict_types=1);

namespace Tulia\Cms\User\UserInterface\Web\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Content\Type\UserInterface\Web\Backend\Form\FormAttributesExtractor;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Security\Framework\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Cms\User\Application\Service\AuthenticatedUserProviderInterface;
use Tulia\Cms\User\Application\UseCase\ChangePassword;
use Tulia\Cms\User\Application\UseCase\ChangePasswordRequest;
use Tulia\Cms\User\Application\UseCase\UpdateMyAccount;
use Tulia\Cms\User\Application\UseCase\UpdateMyAccountRequest;
use Tulia\Cms\User\Domain\WriteModel\UserRepositoryInterface;
use Tulia\Cms\User\UserInterface\Web\Form\MyAccountDetailsForm;
use Tulia\Cms\User\UserInterface\Web\Form\PasswordForm;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteInterface;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class MyAccount extends AbstractController
{
    public function __construct(
        private readonly AuthenticatedUserProviderInterface $authenticatedUserProvider,
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    public function me(): ViewInterface
    {
        return $this->view('@backend/user/me/me.tpl', [
            'user' => $this->authenticatedUserProvider->getUser(),
        ]);
    }

    public function personalization(): ViewInterface
    {
        return $this->view('@backend/user/me/personalization.tpl', [
            'user' => $this->authenticatedUserProvider->getUser(),
        ]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse|ViewInterface
     * @CsrfToken(id="my_account_details_form")
     */
    public function edit(
        Request $request,
        UpdateMyAccount $updateMyAccount,
        WebsiteInterface $website,
        FormAttributesExtractor $extractor,
    ) {
        $user = $this->userRepository->get($this->authenticatedUserProvider->getUser()->getId());

        if (!$user) {
            return $this->redirectToRoute('backend.homepage');
        }

        $form = $this->createForm(
            MyAccountDetailsForm::class,
            $user->toArray(),
            [
                'website' => $website,
                'content_type' => 'user',
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            ($updateMyAccount)(UpdateMyAccountRequest::fromArray(
                array_merge(
                    $extractor->extractData($form, 'user'),
                    ['id' => $user->getId()],
                )
            ));

            $this->addFlash('success', $this->trans('userSaved', [], 'users'));
            return $this->redirectToRoute('backend.me.edit');
        }

        return $this->view('@backend/user/me/edit.tpl', [
            'user' => $this->authenticatedUserProvider->getUser(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @CsrfToken(id="password_form")
     */
    public function password(Request $request, ChangePassword $changePassword)
    {
        $form = $this->createForm(PasswordForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if ($this->authenticatedUserProvider->isPasswordValid($data['current_password']) === false) {
                $this->addFlash('danger', $this->trans('pleaseTypeValidCurrentPasswordToDoThisOperation', [], 'users'));
                return $this->redirectToRoute('backend.me.password');
            }

            ($changePassword)(new ChangePasswordRequest($this->authenticatedUserProvider->getUser()->getId(), $data['new_password']));

            $this->addFlash('danger', $this->trans('passwordChangedSuccessfully', [], 'users'));
            return $this->redirectToRoute('backend.logout');
        }

        return $this->view('@backend/user/me/password.tpl', [
            'user' => $this->authenticatedUserProvider->getUser(),
            'form' => $form->createView(),
        ]);
    }
}
