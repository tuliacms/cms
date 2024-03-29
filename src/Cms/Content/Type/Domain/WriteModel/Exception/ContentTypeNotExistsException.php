<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\WriteModel\Exception;

use Tulia\Cms\Shared\Domain\WriteModel\Exception\AbstractDomainException;

/**
 * @author Adam Banaszkiewicz
 */
class ContentTypeNotExistsException extends AbstractDomainException
{
    private string $type;

    public static function fromType(string $type): self
    {
        $self = new self(sprintf('Content type "%s" not exists.', $type));
        $self->type = $type;

        return $self;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
