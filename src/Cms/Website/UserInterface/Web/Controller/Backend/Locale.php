<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\UserInterface\Web\Controller\Backend;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Security\Framework\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Cms\Website\Application\UseCase\ActivateLocale;
use Tulia\Cms\Website\Application\UseCase\ActivateLocaleRequest;
use Tulia\Cms\Website\Application\UseCase\AddLocale;
use Tulia\Cms\Website\Application\UseCase\AddLocaleRequest;
use Tulia\Cms\Website\Application\UseCase\DeactivateLocale;
use Tulia\Cms\Website\Application\UseCase\DeactivateLocaleRequest;
use Tulia\Cms\Website\Application\UseCase\DeactivateWebsite;
use Tulia\Cms\Website\Application\UseCase\DeactivateWebsiteRequest;
use Tulia\Cms\Website\Application\UseCase\DeleteLocale;
use Tulia\Cms\Website\Application\UseCase\DeleteLocaleRequest;
use Tulia\Cms\Website\Domain\WriteModel\Exception\CannotAddLocaleException;
use Tulia\Cms\Website\Domain\WriteModel\Exception\CannotDeleteLocaleException;
use Tulia\Cms\Website\Domain\WriteModel\Exception\WebsiteNotFoundException;
use Tulia\Cms\Website\UserInterface\Web\Form\AddLocaleForm;

/**
 * @author Adam Banaszkiewicz
 */
final class Locale extends AbstractController
{
    /**
     * @CsrfToken(id="add_locale_form")
     */
    public function create(Request $request, AddLocale $addLocale): JsonResponse
    {
        $form = $this->createForm(AddLocaleForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            try {
                ($addLocale)(
                    new AddLocaleRequest(
                        $data['website'],
                        $data['code'],
                        $data['domain'],
                        $data['domainDevelopment'],
                        $data['localePrefix'],
                        $data['pathPrefix'],
                        $data['sslMode'],
                    )
                );
            } catch (CannotAddLocaleException $e) {
                return new JsonResponse(['errors' => ['code' => [$this->trans($e->reason, [], 'websites')] ]], Response::HTTP_BAD_REQUEST);
            }

            $this->setFlash('success', $this->trans('websiteSaved', [], 'websites'));
            return new JsonResponse([], Response::HTTP_OK);
        }

        return new JsonResponse(['errors' => $this->getErrorMessages($form)], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @CsrfToken(id="website.locale.deactivate")
     */
    public function deactivate(Request $request, DeactivateLocale $deactivateLocale): RedirectResponse
    {
        try {
            $deactivateLocale(new DeactivateLocaleRequest($request->request->get('website'), $request->request->get('code')));
            $this->setFlash('success', $this->trans('activityChanged', [], 'websites'));
        } catch (WebsiteNotFoundException $e) {
            $this->setFlash('danger', $this->trans('websiteNotFound', [], 'websites'));
        }

        return $this->redirectToRoute('backend.website');
    }

    /**
     * @CsrfToken(id="website.locale.activate")
     */
    public function activate(Request $request, ActivateLocale $activateLocale): RedirectResponse
    {
        try {
            $activateLocale(new ActivateLocaleRequest($request->request->get('website'), $request->request->get('code')));
            $this->setFlash('success', $this->trans('activityChanged', [], 'websites'));
        } catch (WebsiteNotFoundException $e) {
            $this->setFlash('danger', $this->trans('websiteNotFound', [], 'websites'));
        }

        return $this->redirectToRoute('backend.website');
    }

    /**
     * @CsrfToken(id="website.locale.delete")
     */
    public function delete(Request $request, DeleteLocale $deleteLocale): RedirectResponse
    {
        try {
            $deleteLocale(new DeleteLocaleRequest($request->request->get('website'), $request->request->get('locale')));
            $this->setFlash('success', $this->trans('localeWasDeleted', [], 'websites'));
        } catch (CannotDeleteLocaleException $e) {
            $this->cannotDoThisBecause('cannotDeleteLocaleBecause', $e->reason);
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
}
