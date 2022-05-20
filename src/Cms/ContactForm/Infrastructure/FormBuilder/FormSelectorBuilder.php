<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Infrastructure\FormBuilder;

use Tulia\Cms\ContactForm\Domain\ReadModel\Finder\ContactFormFinderInterface;
use Tulia\Cms\ContactForm\Domain\ReadModel\Finder\ContactFormFinderScopeEnum;
use Tulia\Cms\ContentBuilder\ContentType\Domain\ReadModel\FieldTypeBuilder\AbstractFieldTypeBuilder;
use Tulia\Cms\ContentBuilder\ContentType\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\ContentBuilder\ContentType\Domain\ReadModel\Model\Field;

/**
 * @author Adam Banaszkiewicz
 */
class FormSelectorBuilder extends AbstractFieldTypeBuilder
{
    private ContactFormFinderInterface $finder;

    public function __construct(ContactFormFinderInterface $finder)
    {
        $this->finder = $finder;
    }

    public function buildOptions(Field $field, array $options, ContentType $contentType): array
    {
        $forms = $this->finder->find([], ContactFormFinderScopeEnum::SEARCH);

        foreach ($forms as $form) {
            $options['choices'][$form->getName()] = $form->getId();
        }

        return $options;
    }
}
