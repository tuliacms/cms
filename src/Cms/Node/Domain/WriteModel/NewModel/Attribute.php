<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\NewModel;

use Tulia\Cms\Content\Attributes\Domain\WriteModel\Model\Attribute as BaseAttribute;

/**
 * @author Adam Banaszkiewicz
 * @final
 */
class Attribute extends BaseAttribute
{
    private string $id;
    private string $locale;
    private ?NodeTranslation $node;

    public static function fromCore(NodeTranslation $node, BaseAttribute $attribute, string $locale): self
    {
        $self = new static(
            $attribute->code,
            $attribute->uri,
            $attribute->value,
            $attribute->compiledValue,
            $attribute->payload,
            $attribute->flags
        );
        $self->node = $node;
        $self->locale = $locale;

        return $self;
    }

    public function detach(): void
    {
        $this->node = null;
    }
}
