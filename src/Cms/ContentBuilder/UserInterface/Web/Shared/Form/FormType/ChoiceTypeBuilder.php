<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\Web\Shared\Form\FormType;

use Tulia\Cms\ContentBuilder\Domain\ReadModel\FieldTypeBuilder\FieldTypeBuilderInterface;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\Field;

/**
 * @author Adam Banaszkiewicz
 */
class ChoiceTypeBuilder implements FieldTypeBuilderInterface
{
    public function build(Field $field, array $options, ContentType $contentType): array
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
