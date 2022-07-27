<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Domain\WriteModel\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Tulia\Cms\Content\Attributes\Domain\WriteModel\Model\Attribute as CoreAttribute;
use Tulia\Cms\Content\Attributes\Domain\WriteModel\Model\AttributesAwareAggregateTrait;
use Tulia\Cms\Shared\Domain\WriteModel\Model\AbstractAggregateRoot;
use Tulia\Cms\User\Application\Service\Avatar\UploaderInterface;
use Tulia\Cms\User\Domain\WriteModel\Event;
use Tulia\Cms\User\Domain\WriteModel\Event\UserDeleted;
use Tulia\Cms\User\Domain\WriteModel\Exception\CannotDeleteUserException;
use Tulia\Cms\User\Domain\WriteModel\Exception\EmailEmptyException;
use Tulia\Cms\User\Domain\WriteModel\Exception\EmailInvalidException;
use Tulia\Cms\User\Domain\WriteModel\Rules\CanDeleteUser\CanDeleteUserInterface;
use Tulia\Cms\User\Domain\WriteModel\Rules\CanDeleteUser\CanDeleteUserReasonEnum;

/**
 * @author Adam Banaszkiewicz
 */
class User extends AbstractAggregateRoot
{
    use AttributesAwareAggregateTrait;

    private string $id;
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
    protected Collection $attributes;

    private function __construct(string $id, string $email, string $password, array $roles)
    {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->roles = $roles;
        $this->attributes = new ArrayCollection();
    }

    public static function create(
        string $id,
        string $email,
        string $password,
        array $roles,
        bool $enabled = true,
        string $locale = 'en_US',
        array $attributes = [],
        ?string $name = null
    ): self {
        $self = new self($id, $email, $password, $roles);
        $self->enabled = $enabled;
        $self->locale = $locale;
        $self->attributes = new ArrayCollection(array_map(
            fn($v) => $self->factoryAttributeFromCore($v),
            $attributes
        ));
        $self->name = $name;
        $self->recordThat(new Event\UserCreated($id));

        return $self;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'locale' => $this->locale,
            'enabled' => $this->enabled,
            'accountExpired' => $this->accountExpired,
            'credentialsExpired' => $this->credentialsExpired,
            'accountLocked' => $this->accountLocked,
            'roles' => $this->roles,
            'password' => $this->password,
            'attributes' => $this->attributesToArray(),
            'name' => $this->name,
            'avatar' => $this->avatar,
        ];
    }

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $role
     */
    public function giveARole(string $role): void
    {
        if (in_array($role, $this->roles) === false) {
            $this->roles[] = $role;

            $this->recordThat(new Event\RoleWasGiven($this->id, $role));
        }
    }

    /**
     * @param string $role
     */
    public function takeARole(string $role): void
    {
        $key = array_search($role, $this->roles);

        if ($key !== false) {
            unset($this->roles[$key]);

            $this->recordThat(new Event\RoleWasTaken($this->id, $role));
        }
    }

    /**
     * @param array $roles
     */
    public function persistRoles(array $roles): void
    {
        $new = array_diff($roles, $this->roles);
        $old = array_diff($this->roles, $roles);

        foreach ($new as $role) {
            $this->giveARole($role);
        }

        foreach ($old as $role) {
            $this->takeARole($role);
        }
    }

    /**
     * @param string $password
     */
    public function changePassword(string $password): void
    {
        /**
         * Password cannot be empty. If empty password provided, user do
         * not want to update it. Security layer updated password only when
         * it's provided.
         */
        if ($this->password !== $password && empty($password) === false) {
            $this->password = $password;

            $this->recordThat(new Event\PasswordChanged($this->id));
        }
    }

    /**
     * @param string $email
     *
     * @throws EmailEmptyException
     * @throws EmailInvalidException
     */
    public function changeEmail(string $email): void
    {
        if (empty($email)) {
            throw new EmailEmptyException('Email address must be provided, cannot be empty.');
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new EmailInvalidException('Email address is invalid.');
        }

        if ($this->email !== $email) {
            $this->email = $email;

            $this->recordThat(new Event\EmailChanged($this->id, $email));
        }
    }

    /**
     * @param string $locale
     */
    public function changeLocale(string $locale): void
    {
        if ($this->locale !== $locale) {
            $this->locale = $locale;

            $this->recordThat(new Event\LocaleChanged($this->id, $locale));
        }
    }

    public function disableAccount(): void
    {
        if ($this->enabled) {
            $this->enabled = false;

            $this->recordThat(new Event\AccountWasDisabled($this->id));
        }
    }

    public function enableAccount(): void
    {
        if (!$this->enabled) {
            $this->enabled = true;

            $this->recordThat(new Event\AccountWasEnabled($this->id));
        }
    }

    public function changeAvatar(string $avatar): void
    {
        $this->avatar = $avatar;
    }

    public function changeName(?string $name): void
    {
        $this->name = $name;
    }

    public function removeAvatar(UploaderInterface $uploader): void
    {
        if ($this->avatar) {
            $uploader->removeUploaded($this->avatar);
            $this->avatar = null;
        }
    }

    public function delete(CanDeleteUserInterface $rules, UploaderInterface $uploader): void
    {
        $reason = $rules->decide($this->id);

        if (CanDeleteUserReasonEnum::OK !== $reason) {
            throw CannotDeleteUserException::fromReason($reason, $this->id);
        }

        if ($this->avatar) {
            $uploader->removeUploaded($this->avatar);
        }

        $this->recordThat(new UserDeleted($this->id));
    }

    protected function factoryAttributeFromCore(CoreAttribute $attribute): Attribute
    {
        return Attribute::fromCore($this, $attribute);
    }

    protected function noticeThatAttributeHasBeenAdded(CoreAttribute $attribute): void {}

    protected function noticeThatAttributeHasBeenRemoved(CoreAttribute $attribute): void {}

    protected function noticeThatAttributeHasBeenUpdated(CoreAttribute $attribute): void {}
}
