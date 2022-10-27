<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Framework\Form\FormType\UserAvatar;

use Symfony\Component\Form\DataTransformerInterface;
use Tulia\Cms\User\Application\Service\Avatar\UploaderInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class UserAvatarModelTransformer implements DataTransformerInterface
{
    public function __construct(
        private UploaderInterface $uploader
    ) {
    }

    public function transform(mixed $value): mixed
    {
        return $value
            ? new UserAvatarFile($this->uploader->getFilepath($value), true, $value)
            : null;
    }

    public function reverseTransform(mixed $value): mixed
    {
        return $value;
    }
}
