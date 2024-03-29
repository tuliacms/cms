<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\UserInterface\Web\Backend\Form;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\FieldTypeMappingRegistry;
use Tulia\Cms\Content\Type\Infrastructure\Framework\Form\Validator\CodenameValidator;
use Tulia\Cms\Content\Type\UserInterface\Web\Backend\Form\FormType\ContentTypeForm;
use Tulia\Cms\Content\Type\UserInterface\Web\Backend\Form\FormType\LayoutSectionType;

/**
 * @author Adam Banaszkiewicz
 */
class FormHandler
{
    private FieldTypeMappingRegistry $fieldTypeMappingRegistry;
    private FormFactoryInterface $formFactory;
    private array $cleaningResult = [];
    private array $errors = [];
    private bool $isRequestValid = false;

    public function __construct(
        FieldTypeMappingRegistry $fieldTypeMappingRegistry,
        FormFactoryInterface $formFactory
    ) {
        $this->fieldTypeMappingRegistry = $fieldTypeMappingRegistry;
        $this->formFactory = $formFactory;
    }

    public function isRequestValid(): bool
    {
        return $this->isRequestValid;
    }

    public function handle(Request $request, array $data, bool $editForm = false): array
    {
        $this->isRequestValid = false;

        if ($request->isMethod('POST') === false) {
            return $data;
        }

        $errors = [];
        $data = json_decode($request->request->get('node_type'), true);

        $validationDataManipulator = new ValidationRequestManipulator();
        $formData = $validationDataManipulator->cleanFromValidationData($data);

        $dataManipulator = new RequestDataValidator(
            $formData,
            $this->fieldTypeMappingRegistry,
            new CodenameValidator()
        );
        $formData = $dataManipulator->cleanForInvalidElements();
        $this->cleaningResult = $dataManipulator->getCleaningResult();

        $formsAreValid = true;

        // Node type form
        $duplicatedRequest = $request->duplicate();
        $duplicatedRequest->request->set('content_type_form', $formData['type']);
        $form = $this->formFactory->create(ContentTypeForm::class, null, [
            'fields' => $this->collectFieldsFromSections($formData['layout']),
            'edit_form' => $editForm,
        ]);
        $form->handleRequest($duplicatedRequest);
        if ($form->isSubmitted() && $form->isValid()) {
            // Do nothing if valid
        } else {
            $formsAreValid = false;
            $errors['type'] = $this->getErrorMessages($form);
        }


        // Layout sidebar section form
        $duplicatedRequest = $request->duplicate();
        $duplicatedRequest->request->set('layout_section', $formData['layout']['sidebar']);
        $form = $this->formFactory->create(LayoutSectionType::class, [], ['max_depth_fields' => 5]);
        $form->handleRequest($duplicatedRequest);
        if ($form->isSubmitted() && $form->isValid()) {
            // Do nothing if valid
        } else {
            $formsAreValid = false;
            $errors['layout']['sidebar'] = $this->getErrorMessages($form);
        }


        // Layout main section form
        $duplicatedRequest = $request->duplicate();
        $duplicatedRequest->request->set('layout_section', $formData['layout']['main']);
        $form = $this->formFactory->create(LayoutSectionType::class, [], ['max_depth_fields' => 5]);
        $form->handleRequest($duplicatedRequest);
        if ($form->isSubmitted() && $form->isValid()) {
            // Do nothing if valid
        } else {
            $formsAreValid = false;
            $errors['layout']['main'] = $this->getErrorMessages($form);
        }

        if ($formsAreValid) {
            $this->isRequestValid = true;
        }

        $this->errors = $errors;

        return $validationDataManipulator->joinErrorsWithData($formData, $errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getCleaningResult(): array
    {
        return $this->cleaningResult;
    }

    private function getErrorMessages(FormInterface $form): array {
        $errors = [];

        foreach ($form->getErrors() as $error) {
            if ($form->isRoot()) {
                $errors['#'][] = $error->getMessage();
            } else {
                $errors[] = $error->getMessage();
            }
        }

        foreach ($form->all() as $child) {
            if (!$child->isSubmitted() || !$child->isValid()) {
                $errors[$child->getName()] = $this->getErrorMessages($child);
            }
        }

        return $errors;
    }

    private function collectFieldsFromSections(array $layout): array
    {
        $fields = [];

        foreach ($layout as $group) {
            foreach ($group['sections'] as $section) {
                $fields += $section['fields'];
            }
        }

        return $fields;
    }
}
