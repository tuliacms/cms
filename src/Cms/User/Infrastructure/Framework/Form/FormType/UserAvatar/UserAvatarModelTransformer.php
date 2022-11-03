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
        private readonly UploaderInterface $uploader,
    ) {
    }

    public function transform(mixed $value): mixed
    {
        $filepath = $this->uploader->getFilepath($value);

        return is_file($filepath)
            ? new UserAvatarFile($filepath, true, $value)
            : null;
    }

    public function reverseTransform(mixed $value): mixed
    {
        return $value;
    }
}
