<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\UserInterface\Web\Controller\Backend;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Security\Framework\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Cms\Website\Application\UseCase\CreateWebsite;
use Tulia\Cms\Website\Application\UseCase\CreateWebsiteRequest;
use Tulia\Cms\Website\Application\UseCase\DeleteWebsite;
use Tulia\Cms\Website\Application\UseCase\DeleteWebsiteRequest;
use Tulia\Cms\Website\Application\UseCase\UpdateWebsite;
use Tulia\Cms\Website\Application\UseCase\UpdateWebsiteRequest;
use Tulia\Cms\Website\Domain\ReadModel\Finder\WebsiteFinderScopeEnum;
use Tulia\Cms\Website\Domain\WriteModel\Exception\CannotDeleteWebsiteException;
use Tulia\Cms\Website\Domain\WriteModel\Exception\CannotTurnOffWebsiteException;
use Tulia\Cms\Website\Domain\WriteModel\WebsiteRepositoryInterface;
use Tulia\Cms\Website\Infrastructure\Persistence\Doctrine\Dbal\Finder\DbalFinder;
use Tulia\Cms\Website\UserInterface\Web\Form\WebsiteForm;
use Tulia\Cms\Website\UserInterface\Web\Service\WebsiteRequestExtractor;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Website extends AbstractController
{
    public function __construct(
        private DbalFinder $finder,
        private WebsiteRepositoryInterface $repository,
        private WebsiteRequestExtractor $requestExtractor
    ) {
    }

    public function index(): RedirectResponse
    {
        return $this->redirectToRoute('backend.website.list');
    }

    /**
     * @return RedirectResponse|ViewInterface
     */
    public function list()
    {
        $result = $this->finder->find([], WebsiteFinderScopeEnum::BACKEND_LISTING);

        return $this->view('@backend/website/list.tpl', [
            'websites' => $result,
        ]);
    }

    /**
     * @return RedirectResponse|ViewInterface
     * @CsrfToken(id="website_form")
     */
    public function create(Request $request, CreateWebsite $createWebsite)
    {
        $this->denyIfNotDevelopmentEnvironment();

        $form = $this->createForm(WebsiteForm::class, [
            'id' => $this->repository->getNextId(),
            'locales' => [
                [
                    'domain' => $request->getHttpHost(),
                    'code' => $request->getPreferredLanguage(),
                    'is_default' => true,
                ]
            ]
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $data = $form->getData();

                ($createWebsite)(
                    new CreateWebsiteRequest(
                        $data['name'],
                        (bool)$data['active'],
                        $data['locales'],
                    )
                );
            } catch (CannotTurnOffWebsiteException $e) {
                $this->cannotDoThisBecause('cannotTurnOffWebsiteBecause', $e->reason);
                return $this->redirectToRoute('backend.website.edit', ['id' => $id]);
            }

            $this->setFlash('success', $this->trans('websiteSaved', [], 'websites'));
            return $this->redirectToRoute('backend.website');
        }

        return $this->view('@backend/website/create.tpl', [
            'form'    => $form->createView(),
            'locale_defaults' => [
                'domain' => $request->getHttpHost(),
                'locale' => $request->getPreferredLanguage(),
            ],
        ]);
    }

    /**
     * @return RedirectResponse|ViewInterface
     * @CsrfToken(id="website_form")
     */
    public function edit(string $id, Request $request, UpdateWebsite $updateWebsite)
    {
        $this->denyIfNotDevelopmentEnvironment();

        $website = $this->repository->get($id);
        $form = $this->createForm(WebsiteForm::class, $website->toArray());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            try {
                ($updateWebsite)(
                    new UpdateWebsiteRequest(
                        $id,
                        $data['name'],
                        (bool)$data['active'],
                        $data['locales'],
                    )
                );
            } catch (CannotTurnOffWebsiteException $e) {
                $this->cannotDoThisBecause('cannotTurnOffWebsiteBecause', $e->reason);
                return $this->redirectToRoute('backend.website.edit', ['id' => $id]);
            }

            $this->setFlash('success', $this->trans('websiteSaved', [], 'websites'));
            return $this->redirectToRoute('backend.website');
        }

        return $this->view('@backend/website/edit.tpl', [
            'website' => $website,
            'form'    => $form->createView(),
            'locale_defaults' => [
                'domain' => $request->getHttpHost(),
                'locale' => $request->getPreferredLanguage(),
            ],
        ]);
    }

    /**
     * @CsrfToken(id="website.delete")
     */
    public function delete(Request $request, DeleteWebsite $deleteWebsite): RedirectResponse
    {
        $this->denyIfNotDevelopmentEnvironment();

        try {
            ($deleteWebsite)(new DeleteWebsiteRequest($request->request->get('id')));
            $this->setFlash('success', $this->trans('selectedWebsitesWereDeleted', [], 'websites'));
        } catch (CannotDeleteWebsiteException $e) {
            $this->cannotDoThisBecause('cannotDeleteWebsiteBecause', $e->reason);
        }

        return $this->redirectToRoute('backend.website');
    }

    private function cannotDoThisBecause(string $message, string $reason): void
    {
        $this->setFlash('warning', $this->trans($message, ['reason' => $this->trans($reason, [], 'websites')], 'websites'));
    }
}
