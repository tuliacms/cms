<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Domain\WriteModel\Model;

use Tulia\Cms\Content\Attributes\Domain\WriteModel\Model\Attribute as CoreAttribute;

/**
 * @author Adam Banaszkiewicz
 * @final
 */
class Attribute extends CoreAttribute
{
    private string $id;
    private ?User $user;

    public static function fromCore(User $user, CoreAttribute $attribute): self
    {
        $self = new static(
            $attribute->code,
            $attribute->uri,
            $attribute->value,
            $attribute->compiledValue,
            $attribute->payload,
            $attribute->flags
        );
        $self->user = $user;

        return $self;
    }

    public function detach(): void
    {
        $this->user = null;
    }
}
