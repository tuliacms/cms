<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Application\UseCase;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tulia\Cms\Shared\Application\UseCase\RequestInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class UpdateMyAccountRequest implements RequestInterface
{
    public function __construct(
        public readonly string $id,
        public readonly string $locale = 'en_US',
        public readonly ?string $name = null,
        public readonly ?UploadedFile $avatar = null,
        public readonly bool $removeAvatar = false,
        public readonly array $attributes = [],
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            locale: $data['locale'] ?? null,
            name: $data['name'] ?? null,
            avatar: $data['avatar'] ?? null,
            removeAvatar: $data['remove_avatar'] ?? null,
            attributes: $data['attributes'] ?? [],
        );
    }
}
