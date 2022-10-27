<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\UserInterface\Web\Backend\Controller;

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\ContactForm\Application\UseCase\CreateForm;
use Tulia\Cms\ContactForm\Application\UseCase\CreateFormRequest;
use Tulia\Cms\ContactForm\Application\UseCase\DeleteForm;
use Tulia\Cms\ContactForm\Application\UseCase\DeleteFormRequest;
use Tulia\Cms\ContactForm\Application\UseCase\UpdateForm;
use Tulia\Cms\ContactForm\Application\UseCase\UpdateFormRequest;
use Tulia\Cms\ContactForm\Domain\Exception\FormNotFoundException;
use Tulia\Cms\ContactForm\Domain\FieldsParser\Exception\InvalidFieldNameException;
use Tulia\Cms\ContactForm\Domain\FieldsParser\Exception\MultipleFieldsInTemplateException;
use Tulia\Cms\ContactForm\Domain\FieldType\FieldsTypeRegistryInterface;
use Tulia\Cms\ContactForm\Domain\ReadModel\Query\AvailableContactFormsQueryInterface;
use Tulia\Cms\ContactForm\Domain\WriteModel\ContactFormRepositoryInterface;
use Tulia\Cms\ContactForm\Infrastructure\Persistence\Domain\ReadModel\Datatable\DatatableFinder;
use Tulia\Cms\ContactForm\UserInterface\Web\Backend\Form\ContactFormForm;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Security\Framework\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Cms\Shared\Application\UseCase\IdResult;
use Tulia\Component\Datatable\DatatableFactory;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteInterface;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Form extends AbstractController
{
    public function __construct(
        private readonly ContactFormRepositoryInterface $repository,
        private readonly FieldsTypeRegistryInterface $typesRegistry,
        private readonly AvailableContactFormsQueryInterface $availableContactFormsQuery
    ) {
    }

    public function index(): RedirectResponse
    {
        return $this->redirectToRoute('backend.contact_form.list');
    }

    public function list(Request $request, DatatableFactory $factory, DatatableFinder $finder, WebsiteInterface $website): ViewInterface
    {
        return $this->view('@backend/forms/index.tpl', [
            'datatable' => $factory->create($finder, $request)->generateFront(['website' => $website]),
        ]);
    }

    public function datatable(Request $request, DatatableFactory $factory, DatatableFinder $finder, WebsiteInterface $website): JsonResponse
    {
        return $factory->create($finder, $request)->generateResponse(['website' => $website]);
    }

    /**
     * @CsrfToken(id="contact_form_form")
     */
    public function create(Request $request, CreateForm $createForm, WebsiteInterface $website)
    {
        if (!$website->isDefaultLocale()) {
            $this->addFlash('info', $this->trans('youHaveBeenRedirectedToDefaultLocaleDueToCreationMultilingualElement'));
            return $this->redirectToRoute('backend.contact_form.create', ['_locale' => $website->getDefaultLocale()->getCode()]);
        }

        $formData = $this->getDefaultFormData();
        $form = $this->createForm(ContactFormForm::class, $formData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $data = $form->getData();

                /** @var IdResult $result */
                $result = ($createForm)(new CreateFormRequest(
                    $data['name'],
                    $data['subject'],
                    $data['receivers'],
                    $data['sender_name'],
                    $data['sender_email'],
                    $data['reply_to'],
                    $data['fields'],
                    $data['fields_template'],
                    $data['message_template'],
                    $website->getId(),
                    $website->getLocale()->getCode(),
                    $website->getDefaultLocale()->getCode(),
                    $website->getLocaleCodes(),
                ));

                $this->addFlash('success', $this->trans('formSaved', [], 'contact-form'));
                return $this->redirectToRoute('backend.contact_form.edit', [ 'id' => $result->id ]);
            } catch (InvalidFieldNameException $e) {
                $error = new FormError($this->trans('formFieldNameContainsInvalidName', ['name' => $e->getName()], 'contact-form'));
                $form->get('fields_template')->addError($error);
            }
        }

        if ($request->request->has('contact_form_form')) {
            $fields = $this->collectFieldsFromRequest($request, $form);
        } else {
            $fields = $this->convertFieldsForViewFormat($formData['fields']);
        }

        return $this->view('@backend/forms/create.tpl', [
            'form' => $form->createView(),
            'fieldTypes' => $this->typesRegistry->all(),
            'fields' => $fields,
            'availableFields' => $this->collectAvailableFields(),
        ]);
    }

    /**
     * @CsrfToken(id="contact_form_form")
     */
    public function edit(string $id, Request $request, UpdateForm $updateForm, WebsiteInterface $website)
    {
        try {
            $model = $this->repository->get($id);
        } catch (FormNotFoundException $e) {
            $this->addFlash('danger', $this->trans('formNotFound', [], 'contact-form'));
            return $this->redirectToRoute('backend.contact_form.list');
        }

        $formData = $model->toArray(
            $website->getLocale()->getCode(),
            $website->getDefaultLocale()->getCode(),
        );

        $form = $this->createForm(ContactFormForm::class, $formData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $data = $form->getData();

                ($updateForm)(new UpdateFormRequest(
                    $id,
                    $data['name'],
                    $data['subject'],
                    $data['receivers'],
                    $data['sender_name'],
                    $data['sender_email'],
                    $data['reply_to'],
                    $data['fields'],
                    $data['fields_template'],
                    $data['message_template'],
                    $website->getLocale()->getCode(),
                    $website->getDefaultLocale()->getCode(),
                ));

                $this->addFlash('success', $this->trans('formSaved', [], 'contact-form'));
                return $this->redirectToRoute('backend.contact_form.edit', [ 'id' => $model->getId() ]);
            } catch (InvalidFieldNameException $e) {
                $error = new FormError($this->trans('formFieldNameContainsInvalidName', ['name' => $e->getName()], 'contact-form'));
                $form->get('fields_template')->addError($error);
            } catch (MultipleFieldsInTemplateException $e) {
                $error = new FormError($this->trans('multipleFieldOccuredInTemplate', ['name' => $e->getName()], 'contact-form'));
                $form->get('fields_template')->addError($error);
            }
        }

        if ($request->request->has('contact_form_form')) {
            $fields = $this->collectFieldsFromRequest($request, $form);
        } else {
            $fields = $this->convertFieldsForViewFormat($formData['fields']);
        }

        return $this->view('@backend/forms/edit.tpl', [
            'model' => $formData,
            'form'  => $form->createView(),
            'fieldTypes' => $this->typesRegistry->all(),
            'fields' => $fields,
            'availableFields' => $this->collectAvailableFields(),
        ]);
    }

    /**
     * @CsrfToken(id="form.delete")
     */
    public function delete(Request $request, DeleteForm $deleteForm): RedirectResponse
    {
        foreach ($request->request->all('ids') as $id) {
            ($deleteForm)(new DeleteFormRequest($id));
        }

        $this->addFlash('success', $this->trans('selectedFormsWereRemoved', [], 'contact-form'));
        return $this->redirectToRoute('backend.contact_form.list');
    }

    public function listForms(): JsonResponse
    {
        return new JsonResponse($this->availableContactFormsQuery->list());
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

    private function collectAvailableFields(): array
    {
        $availableFields = [];

        foreach ($this->typesRegistry->all() as $field) {
            $parser = $this->typesRegistry->getParser($field->getAlias());

            $definition = $parser->getDefinition();
            $alias = $parser->getAlias();

            $availableFields[$alias] = [
                'alias' => $alias,
                'label' => $definition['name'],
                'options' => $definition['options'],
            ];
        }

        return $availableFields;
    }

    private function collectFieldsFromRequest(Request $request, FormInterface $form): array
    {
        if ($form->isSubmitted()) {
            $errors = $this->getErrorMessages($form);
        }

        $fields = [];

        if ($request->isMethod('POST')) {
            $sourceFields = $request->request->all('contact_form_form');
        } else {
            $sourceFields = $form->getData();
        }

        foreach ($sourceFields['fields'] ?? [] as $key => $options) {
            $alias = $options['alias'];
            unset($options['alias']);

            foreach ($options as $name => $value) {
                $options[$name] = [
                    'name' => $name,
                    'value' => $value,
                    'error' => $errors['fields'][$key][$name][0] ?? null,
                ];
            }

            $fields[] = [
                'alias' => $alias,
                'options' => $options,
            ];
        }

        return $fields;
    }

    private function convertFieldsForViewFormat(array $fieldsSource): array
    {
        $fields = [];

        foreach ($fieldsSource as $field) {
            foreach ($field as $name => $value) {
                $options[$name] = [
                    'name' => $name,
                    'value' => $value,
                    'error' => null,
                ];
            }

            $options['name'] = [
                'name' => 'name',
                'value' => $field['name'],
                'error' => null,
            ];

            $fields[] = [
                'alias' => $field['type'],
                'options' => $options,
            ];
        }

        return $fields;
    }

    private function getDefaultFormData(): array
    {
        return [
            'receivers' => [],
            'message_template' => '{{ contact_form_fields() }}',
            'fields_template' => '<div class="mb-3">
    [name]
</div>
<div class="mb-3">
    [message]
</div>
[submit]',
            'fields' => [
                [
                    'type' => 'text',
                    'name' => 'name',
                    'label' => 'Name',
                    'constraints' => 'required',
                ],
                [
                    'type' => 'textarea',
                    'name' => 'message',
                    'label' => 'Message',
                    'constraints' => 'required',
                ],
                [
                    'type' => 'submit',
                    'name' => 'submit',
                    'label' => 'Submit',
                ],
            ],
        ];
    }
}
