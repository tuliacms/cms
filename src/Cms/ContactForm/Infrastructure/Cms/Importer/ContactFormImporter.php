<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Infrastructure\Cms\Importer;

use Tulia\Cms\ContactForm\Application\UseCase\CreateForm;
use Tulia\Cms\ContactForm\Application\UseCase\CreateFormRequest;
use Tulia\Cms\Shared\Application\UseCase\IdResult;
use Tulia\Component\Importer\ObjectImporter\ObjectImporterInterface;
use Tulia\Component\Importer\ObjectImporter\Traits\WebsiteAwareTrait;
use Tulia\Component\Importer\Structure\ObjectData;

/**
 * @author Adam Banaszkiewicz
 */
final class ContactFormImporter implements ObjectImporterInterface
{
    use WebsiteAwareTrait;

    public function __construct(
        private readonly CreateForm $createForm,
    ) {
    }

    public function import(ObjectData $objectData): ?string
    {
        /** @var IdResult $result */
        $result = ($this->createForm)(new CreateFormRequest(
            $objectData['name'],
            $objectData['subject'],
            $objectData['receivers'],
            $objectData['sender_name'] ?? null,
            $objectData['sender_email'],
            $objectData['reply_to'] ?? null,
            $this->createFieldsCollection($objectData['fields']),
            $objectData['fields_template'],
            $objectData['message_template'],
            $this->getWebsite()->getId(),
            $this->getWebsite()->getLocale()->getCode(),
            $this->getWebsite()->getDefaultLocale()->getCode(),
            $this->getWebsite()->getLocaleCodes(),
        ));

        return $result->id;
    }

    /**
     * @param ObjectData[] $source
     */
    private function createFieldsCollection(array $source): array
    {
        $result = [];

        foreach ($source as $sourceField) {
            $field = $sourceField->toArray() + $sourceField['options'];
            $field['alias'] = $field['type'];

            unset($field['options'], $field['type']);

            foreach ($field as $key => $val) {
                // We have to remove internal fields
                if (str_starts_with($key, '@')) {
                    unset($field[$key]);
                }
            }

            $result[] = $field;
        }

        return $result;
    }
}
