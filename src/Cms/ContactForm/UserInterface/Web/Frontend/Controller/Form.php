<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\UserInterface\Web\Frontend\Controller;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\ContactForm\Domain\ReadModel\Finder\ContactFormFinderInterface;
use Tulia\Cms\ContactForm\Domain\ReadModel\Finder\ContactFormFinderScopeEnum;
use Tulia\Cms\ContactForm\Domain\WriteModel\SenderInterface;
use Tulia\Cms\ContactForm\Infrastructure\FormBuilder\ContactFormBuilderInterface;
use Tulia\Cms\ContactForm\UserInterface\Web\Frontend\Service\FormDataExtractor;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Security\Framework\Security\Http\Csrf\Annotation\IgnoreCsrfToken;
use Tulia\Cms\Shared\Infrastructure\Mail\Exception\MailerConfigurationEmptyException;

/**
 * @author Adam Banaszkiewicz
 */
class Form extends AbstractController
{
    public function __construct(
        private ContactFormBuilderInterface $builder,
        private ContactFormFinderInterface $finder,
        private SenderInterface $sender,
        private FormDataExtractor $dataExtractor,
    ) {
    }

    /**
     * @IgnoreCsrfToken
     */
    public function submit(Request $request, string $id): RedirectResponse
    {
        if ($this->isCsrfTokenValid('contact_form_' . $id, $request->request->all('contact_form_' . $id)['_token'] ?? '') === false) {
            throw $this->createAccessDeniedException('CSRF token is not valid.');
        }

        $model = $this->finder->findOne(['id' => $id, 'fetch_fields' => true], ContactFormFinderScopeEnum::SINGLE);

        if ($model === null) {
            throw $this->createNotFoundException();
        }

        $form = $this->builder->build($model);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $this->dataExtractor->extract($model, $form);

            try {
                if ($this->sender->send($model, $data)) {
                    $this->addFlash(
                        'cms.form.submit_success',
                        $this->trans('formHasBeenSentThankYou', [], 'contact-form')
                    );
                } else {
                    $this->addFlash(
                        'cms.form.submit_failed',
                        $this->trans('formNotHasBeenSentTryAgain', [], 'contact-form')
                    );
                }
            } catch (MailerConfigurationEmptyException $e) {
                dump($e);exit;
                $this->addFlash(
                    'cms.form.submit_failed',
                    $this->trans('formNotHasBeenSentTryAgain', [], 'contact-form')
                );
            } catch (\Throwable $e) {
                dump($e);exit;
            }
        } else {
            $this->addFlash('cms.form.last_errors', json_encode($this->getErrorMessages($form)));
            $this->addFlash('cms.form.last_data', json_encode($form->getData()));
        }

        return $this->redirect($request->headers->get('referer') . '#anchor_contact_form_' . $id);
    }

    private function getErrorMessages(FormInterface $form): array
    {
        $errors = [];

        foreach ($form->getErrors() as $error) {
            if ($form->isRoot()) {
                $errors['#'][] = $error->getMessage();
            } else {
                $errors[] = $error->getMessage();
            }
        }

        foreach ($form->all() as $child) {
            if (! $child->isValid()) {
                $errors[$child->getName()] = $this->getErrorMessages($child);
            }
        }

        return $errors;
    }
}
