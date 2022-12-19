<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\WriteModel\Model;

use Tulia\Cms\Content\Attributes\Domain\WriteModel\Model\Attribute as BaseAttribute;
use Tulia\Cms\Node\Domain\WriteModel\Model\NodeTranslation;

/**
 * @author Adam Banaszkiewicz
 * @final
 */
class Attribute extends BaseAttribute
{
    private string $id;
    private string $locale;
    private ?TermTranslation $termTranslation;

    public static function fromCore(TermTranslation $termTranslation, BaseAttribute $attribute, string $locale): self
    {
        $self = new self(
            $attribute->code,
            $attribute->uri,
            $attribute->value->toRaw(),
            $attribute->compiledValue,
            $attribute->payload,
            $attribute->flags,
        );
        $self->termTranslation = $termTranslation;
        $self->locale = $locale;

        return $self;
    }

    public function detach(): void
    {
        $this->termTranslation = null;
    }
}
