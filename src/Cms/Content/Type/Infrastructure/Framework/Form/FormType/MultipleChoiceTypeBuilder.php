<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Infrastructure\Framework\Form\FormType;

use Tulia\Cms\Content\Type\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\Content\Type\Domain\ReadModel\Model\Field;

/**
 * @author Adam Banaszkiewicz
 */
class MultipleChoiceTypeBuilder extends ChoiceTypeBuilder
{
    public function buildOptions(Field $field, array $options, ContentType $contentType): array
    {
        $options = parent::buildOptions($field, $options, $contentType);
        $options['multiple'] = true;

        return $options;
    }
}
