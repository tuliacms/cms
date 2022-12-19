<?php

declare(strict_types=1);

namespace Tulia\Cms\User\UserInterface\Web\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Content\Type\Infrastructure\Framework\Form\Service\ContentFormService;
use Tulia\Cms\Content\Type\Infrastructure\Framework\Form\Service\SymfonyFieldBuilder;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Security\Framework\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Cms\Shared\Application\UseCase\IdResult;
use Tulia\Cms\User\Application\UseCase\CreateUser;
use Tulia\Cms\User\Application\UseCase\CreateUserRequest;
use Tulia\Cms\User\Application\UseCase\DeleteUser;
use Tulia\Cms\User\Application\UseCase\DeleteUserRequest;
use Tulia\Cms\User\Application\UseCase\UpdateUser;
use Tulia\Cms\User\Application\UseCase\UpdateUserRequest;
use Tulia\Cms\User\Domain\WriteModel\Exception\CannotDeleteUserException;
use Tulia\Cms\User\Domain\WriteModel\UserRepositoryInterface;
use Tulia\Cms\User\Infrastructure\Persistence\Dbal\ReadModel\DbalDatatableFinder;
use Tulia\Cms\User\UserInterface\Web\Form\UserDetailsForm;
use Tulia\Component\Datatable\DatatableFactory;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteInterface;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class User extends AbstractController
{
    public function __construct(
        private readonly ContentFormService $contentFormService,
        private readonly UserRepositoryInterface $repository,
        private readonly SymfonyFieldBuilder $fieldBuilder,
    ) {
    }

    public function index(): RedirectResponse
    {
        return $this->redirectToRoute('backend.user.list');
    }

    public function list(Request $request, DatatableFactory $factory, DbalDatatableFinder $finder): ViewInterface
    {
        return $this->view('@backend/user/user/list.tpl', [
            'datatable' => $factory->create($finder, $request)->generateFront(),
        ]);
    }

    public function datatable(Request $request, DatatableFactory $factory, DbalDatatableFinder $finder): JsonResponse
    {
        return $factory->create($finder, $request)->generateResponse();
    }

    /**
     * @CsrfToken(id="content_builder_form_user")
     */
    public function create(Request $request, CreateUser $createUser, WebsiteInterface $website)
    {
        $userDetailsForm = $this->createForm(UserDetailsForm::class, [], ['csrf_protection' => false]);
        $userDetailsForm->handleRequest($request);

        $formDescriptor = $this->contentFormService->buildFormDescriptor(
            $website,
            'user',
            [],
            [
                'userDetailsForm' => $userDetailsForm,
                'layout' => 'admin',
                'partialView' => '@backend/user/parts/content-type-user-details.tpl',
            ]
        );
        $formDescriptor->handleRequest($request);

        if ($formDescriptor->isFormValid() && $userDetailsForm->isSubmitted() && $userDetailsForm->isValid()) {
            /** @var IdResult $result */
            $result = ($createUser)(CreateUserRequest::fromArray(
                $userDetailsForm->getData() + ['attributes' => $formDescriptor->getData()]
            ));

            $this->addFlash('success', $this->trans('userSaved', [], 'users'));
            return $this->redirectToRoute('backend.user.edit', [ 'id' => $result->id ]);
        }

        return $this->view('@backend/user/user/create.tpl', [
            'formDescriptor' => $formDescriptor,
        ]);
    }

    /**
     * @CsrfToken(id="content_builder_form_user")
     */
    public function edit(Request $request, string $id, UpdateUser $updateUser, WebsiteInterface $website)
    {
        $user = $this->repository->get($id);

        if (! $user) {
            $this->addFlash('danger', $this->trans('userNotExists', [], 'users'));
            return $this->redirectToRoute('backend.user.list');
        }

        $userData = $user->toArray();

        $userDetailsForm = $this->createForm(
            UserDetailsForm::class,
            $userData,
            ['csrf_protection' => false, 'edit_form' => true]
        );
        $userDetailsForm->handleRequest($request);

        $formDescriptor = $this->contentFormService->buildFormDescriptor(
            $website,
            'user',
            $userData['attributes'],
            [
                'userDetailsForm' => $userDetailsForm,
                'layout' => 'admin',
                'partialView' => '@backend/user/parts/content-type-user-details.tpl',
            ]
        );
        $formDescriptor->handleRequest($request);

        if ($formDescriptor->isFormValid()) {
            ($updateUser)(UpdateUserRequest::fromArray(
                array_merge(
                    $userDetailsForm->getData(),
                    ['attributes' => $formDescriptor->getData()]
                )
            ));

            $this->addFlash('success', $this->trans('userSaved', [], 'users'));
            return $this->redirectToRoute('backend.user.edit', [ 'id' => $id ]);
        }

        return $this->view('@backend/user/user/edit.tpl', [
            'formDescriptor' => $formDescriptor,
        ]);
    }

    /**
     * @CsrfToken(id="user.delete")
     */
    public function delete(Request $request, DeleteUser $removeUser): RedirectResponse
    {
        try {
            foreach ($request->request->all()['ids'] ?? [] as $id) {
                ($removeUser)(new DeleteUserRequest($id));
            }
        } catch (CannotDeleteUserException $e) {
            $this->addFlash('danger', $this->trans('cannotDeleteUserBecause', ['reason' => $e->reason->value], 'users'));
            return $this->redirectToRoute('backend.user');
        }

        $this->addFlash('success', $this->trans('selectedUsersWereDeleted', [], 'users'));

        return $this->redirectToRoute('backend.user');
    }
}
