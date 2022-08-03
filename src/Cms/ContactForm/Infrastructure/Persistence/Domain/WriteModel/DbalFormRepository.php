<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Infrastructure\Persistence\Domain\WriteModel;

use Tulia\Cms\ContactForm\Domain\Event\FormDeleted;
use Tulia\Cms\ContactForm\Domain\Exception\FormNotFoundException;
use Tulia\Cms\ContactForm\Domain\WriteModel\ContactFormWriteStorageInterface;
use Tulia\Cms\ContactForm\Domain\WriteModel\FormRepositoryInterface;
use Tulia\Cms\ContactForm\Domain\WriteModel\Model\Field;
use Tulia\Cms\ContactForm\Domain\WriteModel\Model\Form;
use Tulia\Cms\Shared\Domain\WriteModel\UuidGeneratorInterface;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalFormRepository implements FormRepositoryInterface
{
    public function __construct(
        private UuidGeneratorInterface $uuidGenerator,
        private ContactFormWriteStorageInterface $storage,
        private EventBusInterface $eventBus,
    ) {
    }

    public function createNew(string $locale): Form
    {
        return Form::createNew(
            $this->uuidGenerator->generate(),
            $locale,
        );
    }

    /**
     * @throws FormNotFoundException
     */
    public function find(string $id, string $locale): Form
    {
        $form = $this->storage->find($id, $locale);

        if ($form === []) {
            throw new FormNotFoundException(sprintf('Form %s not found.', $id));
        }

        $form['receivers'] = json_decode($form['receivers'], true);
        $form['fields'] = array_map(function ($field) {
            $field['options'] = json_decode($field['options'], true);
            return $field;
        }, $form['fields']);

        return Form::buildFromArray($form);
    }

    public function insert(Form $form): void
    {
        $this->storage->beginTransaction();

        try {
            $this->storage->insert($this->extract($form), $this->currentWebsite->getDefaultLocale()->getCode());
            $this->storage->commit();
        } catch (\Exception $exception) {
            $this->storage->rollback();
            throw $exception;
        }

        $this->eventBus->dispatchCollection($form->collectDomainEvents());
    }

    public function update(Form $form): void
    {
        $this->storage->beginTransaction();

        try {
            $this->storage->update($this->extract($form), $this->currentWebsite->getDefaultLocale()->getCode());
            $this->storage->commit();
        } catch (\Exception $exception) {
            $this->storage->rollback();
            throw $exception;
        }

        $this->eventBus->dispatchCollection($form->collectDomainEvents());
    }

    public function delete(Form $form): void
    {
        $this->storage->beginTransaction();

        try {
            $this->storage->delete($this->extract($form));
            $this->storage->commit();
        } catch (\Exception $exception) {
            $this->storage->rollback();
            throw $exception;
        }

        $this->eventBus->dispatch(FormDeleted::fromForm($form));
    }

    private function extract(Form $form): array
    {
        $result = [
            'id' => $form->getId(),
            'website_id' => $form->getWebsiteId(),
            'locale' => $form->getLocale(),
            'receivers' => json_encode($form->getReceivers()),
            'sender_name' => $form->getSenderName(),
            'sender_email' => $form->getSenderEmail(),
            'reply_to' => $form->getReplyTo(),
            'name' => $form->getName(),
            'subject' => $form->getSubject(),
            'fields_template' => $form->getFieldsTemplate(),
            'fields_view' => $form->getFieldsView(),
            'message_template' => $form->getMessageTemplate(),
            'fields' => [],
        ];

        $itemsChanges = $form->getFieldsChanges();

        foreach ($itemsChanges as $changeData) {
            /** @var Field $field */
            $field = $changeData['entity'];

            $result['fields'][] = [
                '_change_type' => $changeData['type'],
                'id' => $field->getName(),
                'form_id' => $form->getId(),
                'locale' => $form->getLocale(),
                'name' => $field->getName(),
                'type' => $field->getType(),
                'type_alias' => $field->getTypeAlias(),
                'options' => json_encode($field->getOptions()),
            ];
        }

        return $result;
    }
}
