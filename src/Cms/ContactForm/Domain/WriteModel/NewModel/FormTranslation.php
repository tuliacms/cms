<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Domain\WriteModel\NewModel;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Tulia\Cms\ContactForm\Domain\WriteModel\Model\Form;

/**
 * @author Adam Banaszkiewicz
 * @final
 */
class FormTranslation
{
    private string $id;
    private string $subject;
    private string $messageTemplate;
    private string $fieldsView;
    private string $fieldsTemplate;
    /** @var ArrayCollection<int, Field> */
    private Collection $fields;

    public function __construct(
        private Form $form,
        private string $locale
    ) {
        $this->fields = new ArrayCollection();
    }
}
