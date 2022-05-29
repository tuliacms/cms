<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\WriteModel\Exception;

/**
 * @author Adam Banaszkiewicz
 */
class EmptyRoutingStrategyForRoutableContentTypeException extends \DomainException
{
    private string $type;

    public static function fromType(string $type): self
    {
        $self = new self(sprintf('Content type "%s" is routable, and You have to set any Routing Strategy.', $type));
        $self->type = $type;

        return $self;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
