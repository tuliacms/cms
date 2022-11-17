<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Model;

use Tulia\Cms\Content\Attributes\Domain\WriteModel\Model\Attribute as BaseAttribute;

/**
 * @author Adam Banaszkiewicz
 * @final
 */
class Attribute extends BaseAttribute
{
    private string $id;
    private string $locale;
    private ?NodeTranslation $nodeTranslation;

    public static function fromCore(NodeTranslation $nodeTranslation, BaseAttribute $attribute, string $locale): self
    {
        $self = new self(
            $attribute->code,
            $attribute->uri,
            $attribute->value->toRaw(),
            $attribute->compiledValue,
            $attribute->payload,
            $attribute->flags,
        );
        $self->nodeTranslation = $nodeTranslation;
        $self->locale = $locale;

        return $self;
    }

    public function detach(): void
    {
        $this->nodeTranslation = null;
    }
}
