<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Domain\ReadModel\Model;

use Tulia\Cms\Content\Attributes\Domain\ReadModel\LazyMagickAttributesTrait;
use Tulia\Cms\Content\Attributes\Domain\ReadModel\Model\AttributesAwareInterface;

/**
 * @author Adam Banaszkiewicz
 */
class User
{
    protected ?string $id = null;
    protected string $password;
    protected string $email;
    protected string $locale = 'en_US';
    protected bool $enabled = true;
    protected bool $accountExpired = false;
    protected bool $credentialsExpired = false;
    protected bool $accountLocked = false;
    protected array $roles = [];
    protected ?string $name = null;
    protected ?string $avatar = null;

    protected static array $fields = [
        'id'                  => 'id',
        'password'            => 'password',
        'email'               => 'email',
        'roles'               => 'roles',
        'locale'              => 'locale',
        'enabled'             => 'enabled',
        'account_expired'     => 'accountExpired',
        'credentials_expired' => 'credentialsExpired',
        'account_locked'      => 'accountLocked',
        'name'                => 'name',
        'avatar'              => 'avatar',
    ];

    public static function buildFromArray(array $data): self
    {
        $user = new self();

        if (isset($data['id']) === false) {
            throw new \InvalidArgumentException('User ID must be provided.');
        }

        $roles = $data['roles'] ?? [];

        if (\is_string($roles)) {
            $roles = @ json_decode($roles, true);

            if (! $roles) {
                $roles = [];
            }
        }

        $user->setId($data['id']);
        $user->setPassword($data['password'] ?? null);
        $user->setEmail($data['email'] ?? null);
        $user->setLocale($data['locale'] ?? 'en_US');
        $user->setRoles($roles);
        $user->setEnabled((bool) ($data['enabled'] ?? false));
        $user->setAccountExpired((bool) ($data['account_expired'] ?? false));
        $user->setCredentialsExpired((bool) ($data['credentials_expired'] ?? false));
        $user->setAccountLocked((bool) ($data['account_locked'] ?? false));
        $user->name = $data['name'] ?? null;
        $user->avatar = $data['avatar'] ?? null;

        return $user;
    }

    public function toArray(array $params = []): array
    {
        $params = array_merge([
            'skip' => [],
        ], $params);

        $result = [];

        foreach (static::$fields as $key => $property) {
            $result[$key] = $this->{$property};
        }

        foreach ($params['skip'] as $skip) {
            unset($result[$skip]);
        }

        return $result;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function hasId(): bool
    {
        return (bool) $this->id;
    }

    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    public function getAccountExpired(): bool
    {
        return $this->accountExpired;
    }

    public function setAccountExpired(bool $accountExpired): void
    {
        $this->accountExpired = $accountExpired;
    }

    public function getCredentialsExpired(): bool
    {
        return $this->credentialsExpired;
    }

    public function setCredentialsExpired(bool $credentialsExpired): void
    {
        $this->credentialsExpired = $credentialsExpired;
    }

    public function getAccountLocked(): bool
    {
        return $this->accountLocked;
    }

    public function setAccountLocked(bool $accountLocked): void
    {
        $this->accountLocked = $accountLocked;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }
}
