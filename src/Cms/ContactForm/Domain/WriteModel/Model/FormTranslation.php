<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Domain\WriteModel\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @author Adam Banaszkiewicz
 * @final
 */
class FormTranslation
{
    private string $id;
    public string $subject;
    public string $messageTemplate;
    public string $fieldsView;
    public string $fieldsTemplate;
    /** @var ArrayCollection<int, Field> $fields */
    public Collection $fields;
    public bool $translated = false;

    public function __construct(
        private Form $form,
        public string $locale
    ) {
        $this->fields = new ArrayCollection();
    }

    public function fieldsToArray(): array
    {
        $result = [];

        /** @var Field $field */
        foreach ($this->fields->toArray() as $field) {
            $result[] = [
                'name' => $field->name,
                'type' => $field->type,
                'locale' => $field->locale,
            ] + $field->options;
        }

        return $result;
    }
}
