<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Framework\Form\FormType\UserAvatar;

use Symfony\Component\Validator\Constraints as Assert;
use Tulia\Cms\Content\Type\Domain\ReadModel\FieldTypeBuilder\AbstractFieldTypeBuilder;
use Tulia\Cms\Content\Type\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\Content\Type\Domain\ReadModel\Model\Field;

/**
 * @author Adam Banaszkiewicz
 */
class UserAvatarBuilder extends AbstractFieldTypeBuilder
{
    public function buildOptions(Field $field, array $options, ContentType $contentType): array
    {
        $options['constraints'][] = new Assert\Image([
            'minWidth' => 100,
            'minHeight' => 100,
            'maxWidth' => 700,
            'maxHeight' => 700,
            'allowLandscape' => false,
            'allowPortrait' => false,
            'mimeTypes' => ['image/jpg', 'image/jpeg', 'image/png', 'image/gif'],
        ]);

        return $options;
    }
}
