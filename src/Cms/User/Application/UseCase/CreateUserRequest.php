<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Application\UseCase;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tulia\Cms\Shared\Application\UseCase\RequestInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class CreateUserRequest implements RequestInterface
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
        public readonly array $roles,
        public readonly string $locale = 'en_US',
        public readonly bool $enabled = true,
        public readonly ?string $name = null,
        public readonly ?UploadedFile $avatar = null,
        public readonly array $attributes = [],
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            email: $data['email'],
            password: $data['password'],
            locale: $data['locale'] ?? null,
            name: $data['name'] ?? null,
            enabled: (bool) $data['enabled'],
            roles: $data['roles'] ?? [],
            avatar: $data['avatar'] ?? null,
            attributes: $data['attributes'] ?? [],
        );
    }
}
