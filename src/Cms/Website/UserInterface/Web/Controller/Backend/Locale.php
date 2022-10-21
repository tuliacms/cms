<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\UserInterface\Web\Controller\Backend;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Security\Framework\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Cms\Website\Application\UseCase\AddLocale;
use Tulia\Cms\Website\Application\UseCase\AddLocaleRequest;
use Tulia\Cms\Website\Domain\WriteModel\Exception\CannotAddLocaleException;
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
}
