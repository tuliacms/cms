<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Infrastructure\Framework\Form\FormType;

use Tulia\Cms\Content\Type\Domain\ReadModel\FieldTypeBuilder\AbstractFieldTypeBuilder;
use Tulia\Cms\Content\Type\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\Content\Type\Domain\ReadModel\Model\Field;

use function Tulia\Cms\Content\Type\UserInterface\Web\Shared\Form\FormType\count;

/**
 * @author Adam Banaszkiewicz
 */
class ChoiceTypeBuilder extends AbstractFieldTypeBuilder
{
    public function buildOptions(Field $field, array $options, ContentType $contentType): array
    {
        $choicesRaw = $field->getConfig('choices');

        if (is_string($choicesRaw) === false) {
            return $options;
        }

        $choicesRaw = preg_split("/\r\n|\n|\r/", trim($choicesRaw));
        $options['choices'] = [];

        foreach ($choicesRaw as $line => $choice) {
            $data = explode(':', $choice);

            if (is_array($data) && count($data) === 2) {
                $options['choices'][trim($data[1])] = $data[0];
            } else {
                $options['choices'][sprintf('--- option number "%s" is invalid ---', $choice)] = null;
            }
        }

        return $options;
    }
}
