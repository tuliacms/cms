<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Infrastructure\Cms\Importer;

use Tulia\Cms\ContactForm\Domain\ReadModel\Finder\ContactFormFinderInterface;
use Tulia\Cms\ContactForm\Domain\ReadModel\Finder\ContactFormFinderScopeEnum;
use Tulia\Cms\ContactForm\Domain\ReadModel\Model\Form;
use Tulia\Component\Importer\ObjectExporter\ObjectExporterInterface;
use Tulia\Component\Importer\ObjectExporter\ObjectsCollection\ObjectsCollection;
use Tulia\Component\Importer\ObjectExporter\ObjectsCollection\ObjectToExport;
use Tulia\Component\Importer\ObjectExporter\Traits\WebsiteAwareTrait;
use Tulia\Component\Importer\Structure\ObjectData;
use Tulia\Component\Importer\Structure\ObjectDataFactory;

/**
 * @author Adam Banaszkiewicz
 */
final class ContactFormExporter implements ObjectExporterInterface
{
    use WebsiteAwareTrait;

    public function __construct(
        private readonly ContactFormFinderInterface $formFinder,
        private readonly ObjectDataFactory $objectDataFactory,
    ) {
    }

    public function collectObjects(ObjectsCollection $collection): void
    {
        /**
         * We can use Finder here, because there is small chance to existing
         * more then 50 forms in system. This will be fast enough.
         */
        $forms = $this->formFinder->find([
            'website_id' => $this->getWebsite()->getId(),
            'locale' => $this->getWebsite()->getLocale()->getCode(),
        ], ContactFormFinderScopeEnum::INTERNAL);

        /** @var Form $form */
        foreach ($forms->toArray() as $form) {
            $collection->addObject(new ObjectToExport('ContactForm', $form->getId(), $form->getName()));
        }
    }

    public function export(ObjectData $objectData): void
    {
        $form = $this->formFinder->findOne([
            'website_id' => $this->getWebsite()->getId(),
            'locale' => $this->getWebsite()->getLocale()->getCode(),
            'id' => $objectData->getObjectId(),
            'fetch_fields' => true,
        ], ContactFormFinderScopeEnum::INTERNAL);

        $fields = [];

        foreach ($form->getFields() as $field) {
            $fields[] = $this->objectDataFactory->create('ContactFormField', [
                'type' => $field->getType(),
                'name' => $field->getName(),
                'options' => $field->getOptions(),
            ]);
        }

        $objectData['name'] = $form->getName();
        $objectData['receivers'] = $form->getReceivers();
        $objectData['sender_name'] = $form->getSenderName();
        $objectData['sender_email'] = $form->getSenderEmail();
        $objectData['subject'] = $form->getSubject();
        $objectData['message_template'] = $form->getMessageTemplate();
        $objectData['fields_template'] = $form->getFieldsTemplate();
        $objectData['fields'] = $fields;
    }
}
