<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Infrastructure\FormBuilder;

use Tulia\Cms\ContactForm\Domain\ReadModel\Finder\ContactFormFinderInterface;
use Tulia\Cms\ContactForm\Domain\ReadModel\Finder\ContactFormFinderScopeEnum;
use Tulia\Cms\Content\Type\Domain\ReadModel\FieldTypeBuilder\AbstractFieldTypeBuilder;
use Tulia\Cms\Content\Type\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\Content\Type\Domain\ReadModel\Model\Field;

/**
 * @author Adam Banaszkiewicz
 */
class FormSelectorBuilder extends AbstractFieldTypeBuilder
{
    public function __construct(
        private readonly ContactFormFinderInterface $finder,
    ) {
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
