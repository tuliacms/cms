<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UserInterface\Web\Shared\Form\FormType;

use Symfony\Component\Validator\Constraints as Assert;
use Tulia\Cms\ContentBuilder\ContentType\Domain\ReadModel\FieldTypeBuilder\AbstractFieldTypeBuilder;
use Tulia\Cms\ContentBuilder\ContentType\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\ContentBuilder\ContentType\Domain\ReadModel\Model\Field;

/**
 * @author Adam Banaszkiewicz
 */
class NodeStatusTypeBuilder extends AbstractFieldTypeBuilder
{
    public function buildOptions(Field $field, array $options, ContentType $contentType): array
    {
        $options['constraints'] += [
            new Assert\NotBlank(),
            new Assert\Choice([ 'choices' => ['draft', 'published', 'trashed'] ]),
        ];
        $options['choices'] = [
            'Draft' => 'draft',
            'Published' => 'published',
            'Trashed' => 'trashed',
        ];

        return $options;
    }
}
