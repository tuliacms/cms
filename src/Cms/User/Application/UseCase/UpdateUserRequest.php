<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Application\UseCase;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tulia\Cms\Shared\Application\UseCase\RequestInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class UpdateUserRequest implements RequestInterface
{
    public function __construct(
        public readonly string $id,
        public readonly ?string $email = null,
        public readonly ?string $password = null,
        public readonly ?array $roles = null,
        public readonly ?string $locale = null,
        public readonly ?bool $enabled = null,
        public readonly ?string $name = null,
        public readonly ?UploadedFile $avatar = null,
        public readonly bool $removeAvatar = false,
        public readonly ?array $attributes = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            email: $data['email'] ?? null,
            password: $data['password'] ?? null,
            locale: $data['locale'] ?? null,
            name: $data['name'] ?? null,
            enabled: $data['enabled'] ? (bool) $data['enabled'] : null,
            roles: $data['roles'] ?? null,
            avatar: $data['avatar'] ?? null,
            removeAvatar: $data['remove_avatar'] ?? null,
            attributes: $data['attributes'] ?? null,
        );
    }
}
