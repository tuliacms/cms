<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Domain\Event;

use Tulia\Cms\ContactForm\Domain\WriteModel\Model\Form;
use Tulia\Cms\Shared\Domain\WriteModel\Event\AbstractDomainEvent;

/**
 * @author Adam Banaszkiewicz
 */
class FormDeleted extends AbstractDomainEvent
{
    private string $formId;

    public function __construct(string $formId)
    {
        $this->formId = $formId;
    }

    public function getFormId(): string
    {
        return $this->formId;
    }

    public static function fromForm(Form $form): self
    {
        return new self($form->getId()->getValue());
    }
}
