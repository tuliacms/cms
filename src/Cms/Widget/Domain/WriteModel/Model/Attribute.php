<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Domain\WriteModel\Model;

use Tulia\Cms\Content\Attributes\Domain\WriteModel\Model\Attribute as BaseAttribute;

/**
 * @author Adam Banaszkiewicz
 * @final
 */
class Attribute extends BaseAttribute
{
    private string $id;
    private string $locale;
    private ?WidgetTranslation $widgetTranslation;

    public static function fromCore(WidgetTranslation $widgetTranslation, BaseAttribute $attribute, string $locale): self
    {
        $self = new static(
            $attribute->code,
            $attribute->uri,
            $attribute->value->toRaw(),
            $attribute->compiledValue,
            $attribute->payload,
            $attribute->flags
        );
        $self->widgetTranslation = $widgetTranslation;
        $self->locale = $locale;

        return $self;
    }

    public function detach(): void
    {
        $this->widgetTranslation = null;
    }
}
