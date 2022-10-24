<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\UserInterface\Web\Controller\Backend;

use Symfony\Component\Asset\Packages;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteInterface;
use Tulia\Cms\Security\Framework\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Cms\Website\Application\UseCase\ActivateWebsite;
use Tulia\Cms\Website\Application\UseCase\ActivateWebsiteRequest;
use Tulia\Cms\Website\Application\UseCase\CreateWebsite;
use Tulia\Cms\Website\Application\UseCase\CreateWebsiteRequest;
use Tulia\Cms\Website\Application\UseCase\DeactivateWebsite;
use Tulia\Cms\Website\Application\UseCase\DeactivateWebsiteRequest;
use Tulia\Cms\Website\Application\UseCase\DeleteWebsite;
use Tulia\Cms\Website\Application\UseCase\DeleteWebsiteRequest;
use Tulia\Cms\Website\Domain\ReadModel\Finder\WebsiteFinderInterface;
use Tulia\Cms\Website\Domain\WriteModel\Exception\CannotDeleteWebsiteException;
use Tulia\Cms\Website\Domain\WriteModel\Exception\WebsiteNotFoundException;
use Tulia\Cms\Website\Domain\WriteModel\Service\LocaleStorageInterface;
use Tulia\Cms\Website\Domain\WriteModel\WebsiteRepositoryInterface;
use Tulia\Cms\Website\UserInterface\Web\Form\NewWebsiteForm;
use Tulia\Cms\Website\UserInterface\Web\Service\WebsiteRequestExtractor;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Website extends AbstractController
{
    public function __construct(
        private readonly WebsiteFinderInterface $finder,
        private readonly WebsiteRepositoryInterface $repository,
        private readonly WebsiteRequestExtractor $requestExtractor,
        private readonly LocaleStorageInterface $localeStorage,
    ) {
    }

    public function index(): RedirectResponse
    {
        return $this->redirectToRoute('backend.website.list');
    }

    public function list(Packages $packages, WebsiteInterface $website): ViewInterface
    {
        return $this->view('@backend/website/list.tpl', [
            'websites' => $this->collectWebsites($website),
            'locales' => array_map(fn($l) => [
                'code' => $l->getCode(),
                'path_prefix' => $l->getLocalePrefix(),
                'name' => $this->trans('languageName', ['code' => $l->getCode()], 'languages'),
                'flag' => $packages->getUrl(sprintf('/assets/core/flag-icons/%s.svg', $l->getLanguage()))
            ], $this->localeStorage->all()),
        ]);
    }

    /**
     * @CsrfToken(id="new_website_form")
     */
    public function create(Request $request, CreateWebsite $createWebsite): JsonResponse
    {
        $form = $this->createForm(NewWebsiteForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            ($createWebsite)(
                new CreateWebsiteRequest(
                    $data['name'],
                    (bool) $data['activity'],
                    $data['locale'],
                    $data['domain'],
                    $data['backendPrefix'],
                    $data['domainDevelopment'],
                    $data['pathPrefix'],
                    $data['sslMode'],
                )
            );

            $this->setFlash('success', $this->trans('websiteSaved', [], 'websites'));
            return new JsonResponse([], Response::HTTP_OK);
        }

        return new JsonResponse(['errors' => $this->getErrorMessages($form)], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @CsrfToken(id="website.activate")
     */
    public function activate(Request $request, ActivateWebsite $activateWebsite): RedirectResponse
    {
        try {
            $activateWebsite(new ActivateWebsiteRequest($request->request->get('id')));
            $this->setFlash('success', $this->trans('activityChanged', [], 'websites'));
        } catch (WebsiteNotFoundException $e) {
            $this->setFlash('danger', $this->trans('websiteNotFound', [], 'websites'));
        }

        return $this->redirectToRoute('backend.website');
    }

    /**
     * @CsrfToken(id="website.deactivate")
     */
    public function deactivate(Request $request, DeactivateWebsite $deactivateWebsite): RedirectResponse
    {
        try {
            $deactivateWebsite(new DeactivateWebsiteRequest($request->request->get('id')));
            $this->setFlash('success', $this->trans('activityChanged', [], 'websites'));
        } catch (WebsiteNotFoundException $e) {
            $this->setFlash('danger', $this->trans('websiteNotFound', [], 'websites'));
        }

        return $this->redirectToRoute('backend.website');
    }

    /**
     * @CsrfToken(id="website.delete")
     */
    public function delete(Request $request, DeleteWebsite $deleteWebsite): RedirectResponse
    {
        try {
            ($deleteWebsite)(new DeleteWebsiteRequest($request->request->get('id')));
            $this->setFlash('success', $this->trans('websiteWasDeleted', [], 'websites'));
        } catch (CannotDeleteWebsiteException $e) {
            $this->cannotDoThisBecause('cannotDeleteWebsiteBecause', $e->reason);
        }

        return $this->redirectToRoute('backend.website');
    }

    private function getErrorMessages(FormInterface $form): array
    {
        $errors = [];

        foreach ($form->getErrors() as $key => $error) {
            if ($form->isRoot()) {
                $errors['#'][] = $error->getMessage();
            } else {
                $errors[] = $error->getMessage();
            }
        }

        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors[$child->getName()] = $this->getErrorMessages($child);
            }
        }

        return $errors;
    }

    private function cannotDoThisBecause(string $message, string $reason): void
    {
        $this->setFlash('warning', $this->trans($message, ['reason' => $this->trans($reason, [], 'websites')], 'websites'));
    }

    private function collectWebsites(WebsiteInterface $website): array
    {
        $websites = array_map(
            static fn($w) => $w->toArray(),
            $this->finder->all()
        );

        foreach ($websites as $wk => $wv) {
            if ($wv['id'] === $website->getId()) {
                $websites[$wk]['is_current'] = true;
            } else {
                $websites[$wk]['is_current'] = false;
            }

            foreach ($wv['locales'] as $lk => $lv) {
                if ($lv['code'] === $website->getLocale()->getCode()) {
                    $websites[$wk]['locales'][$lk]['is_current'] = true;
                } else {
                    $websites[$wk]['locales'][$lk]['is_current'] = false;
                }
            }
        }

        return $websites;
    }
}
