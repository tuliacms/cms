<?php

declare(strict_types=1);

namespace Tulia\Cms\User\UserInterface\Web\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Content\Type\Infrastructure\Framework\Form\Service\SymfonyFieldBuilder;
use Tulia\Cms\Content\Type\UserInterface\Web\Backend\Form\FormAttributesExtractor;
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
     * @CsrfToken(id="user_details_form")
     */
    public function create(
        Request $request,
        CreateUser $createUser,
        WebsiteInterface $website,
        FormAttributesExtractor $extractor,
    ) {
        $form = $this->createForm(
            UserDetailsForm::class,
            [],
            [
                'partial_view' => '@backend/user/parts/content-type-user-details.tpl',
                'website' => $website,
                'content_type' => 'user',
                'context' => [
                    'layout' => 'admin',
                ],
            ],
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var IdResult $result */
            $result = ($createUser)(CreateUserRequest::fromArray(
                $extractor->extractData($form, 'user'),
            ));

            $this->addFlash('success', $this->trans('userSaved', [], 'users'));
            return $this->redirectToRoute('backend.user.edit', [ 'id' => $result->id ]);
        }

        return $this->view('@backend/user/user/create.tpl', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @CsrfToken(id="user_details_form")
     */
    public function edit(
        Request $request,
        string $id,
        UpdateUser $updateUser,
        WebsiteInterface $website,
        FormAttributesExtractor $extractor,
    ) {
        $user = $this->repository->get($id);

        if (! $user) {
            $this->addFlash('danger', $this->trans('userNotExists', [], 'users'));
            return $this->redirectToRoute('backend.user.list');
        }

        $form = $this->createForm(
            UserDetailsForm::class,
            $user->toArray(),
            [
                'edit_form' => true,
                'partial_view' => '@backend/user/parts/content-type-user-details.tpl',
                'website' => $website,
                'content_type' => 'user',
                'context' => [
                    'layout' => 'admin',
                ],
            ],
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            ($updateUser)(UpdateUserRequest::fromArray(
                $extractor->extractData($form, 'user'),
            ));

            $this->addFlash('success', $this->trans('userSaved', [], 'users'));
            return $this->redirectToRoute('backend.user.edit', [ 'id' => $id ]);
        }

        return $this->view('@backend/user/user/edit.tpl', [
            'form' => $form->createView(),
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
