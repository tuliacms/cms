<?php

declare(strict_types=1);

namespace Tulia\Cms\User\UserInterface\Web\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Content\Type\Infrastructure\Framework\Form\ContentTypeFormDescriptor;
use Tulia\Cms\Content\Type\Infrastructure\Framework\Form\Service\ContentFormService;
use Tulia\Cms\Content\Type\Infrastructure\Framework\Form\Service\SymfonyFieldBuilder;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Security\Framework\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Cms\User\Application\UseCase\CreateUser;
use Tulia\Cms\User\Application\UseCase\CreateUserRequest;
use Tulia\Cms\User\Application\UseCase\DeleteUser;
use Tulia\Cms\User\Application\UseCase\UpdateUser;
use Tulia\Cms\User\Application\UseCase\UpdateUserRequest;
use Tulia\Cms\User\Domain\WriteModel\Exception\CannotDeleteYourselfException;
use Tulia\Cms\User\Domain\WriteModel\Model\User as DomainModel;
use Tulia\Cms\User\Domain\WriteModel\UserRepositoryInterface;
use Tulia\Cms\User\Infrastructure\Persistence\Dbal\ReadModel\DbalDatatableFinder;
use Tulia\Cms\User\UserInterface\Web\Form\UserDetailsForm;
use Tulia\Component\Datatable\DatatableFactory;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class User extends AbstractController
{
    public function __construct(
        private ContentFormService $contentFormService,
        private UserRepositoryInterface $repository,
        private SymfonyFieldBuilder $fieldBuilder
    ) {
    }

    public function index(): RedirectResponse
    {
        return $this->redirectToRoute('backend.user.list');
    }

    public function list(Request $request, DatatableFactory $factory, DbalDatatableFinder $finder): ViewInterface
    {
        return $this->view('@backend/user/user/list.tpl', [
            'datatable' => $factory->create($finder, $request),
        ]);
    }

    public function datatable(Request $request, DatatableFactory $factory, DbalDatatableFinder $finder): JsonResponse
    {
        return $factory->create($finder, $request)->generateResponse();
    }

    /**
     * @CsrfToken(id="content_builder_form_user")
     */
    public function create(Request $request, CreateUser $createUser)
    {
        $userDetailsForm = $this->createForm(UserDetailsForm::class, [], ['csrf_protection' => false]);
        $userDetailsForm->handleRequest($request);

        $formDescriptor = $this->contentFormService->buildFormDescriptor('user', [], ['userDetailsForm' => $userDetailsForm]);
        $formDescriptor->handleRequest($request);

        if ($formDescriptor->isFormValid() && $userDetailsForm->isSubmitted() && $userDetailsForm->isValid()) {
            $result = ($createUser)(CreateUserRequest::fromArray(
                $userDetailsForm->getData() + ['attributes' => $formDescriptor->getData()]
            ));

            $this->setFlash('success', $this->trans('userSaved', [], 'users'));
            return $this->redirectToRoute('backend.user.edit', [ 'id' => $result->id ]);
        }

        return $this->view('@backend/user/user/create.tpl', [
            'formDescriptor' => $formDescriptor,
        ]);
    }

    /**
     * @CsrfToken(id="content_builder_form_user")
     */
    public function edit(Request $request, string $id, UpdateUser $updateUser)
    {
        $user = $this->repository->get($id);

        if (! $user) {
            $this->setFlash('danger', $this->trans('userNotExists', [], 'users'));
            return $this->redirectToRoute('backend.user.list');
        }

        $userData = $user->toArray();

        $userDetailsForm = $this->createForm(
            UserDetailsForm::class,
            $userData,
            ['csrf_protection' => false, 'edit_form' => true]
        );
        $userDetailsForm->handleRequest($request);

        $formDescriptor = $this->contentFormService->buildFormDescriptor('user', $userData['attributes'], ['userDetailsForm' => $userDetailsForm]);
        $formDescriptor->handleRequest($request);

        if ($formDescriptor->isFormValid()) {
            ($updateUser)(UpdateUserRequest::fromArray(
                $userDetailsForm->getData() + ['attributes' => $formDescriptor->getData()]
            ));

            $this->setFlash('success', $this->trans('userSaved', [], 'users'));
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
        $removedUsers = 0;

        foreach ($request->request->get('ids') as $id) {
            $user = $this->repository->get($id);

            if ($user) {
                try {
                    ($removeUser)($user);
                    $removedUsers++;
                } catch (CannotDeleteYourselfException $e) {
                    $this->setFlash('danger', $this->trans('cannotDeleteSelfUser', [], 'users'));
                }
            }
        }

        if ($removedUsers) {
            $this->setFlash('success', $this->trans('selectedUsersWereDeleted', [], 'users'));
        }

        return $this->redirectToRoute('backend.user');
    }
}
