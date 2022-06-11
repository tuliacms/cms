<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Exception;

use Tulia\Cms\Shared\Domain\WriteModel\Exception\AbstractDomainException;

/**
 * @author Adam Banaszkiewicz
 */
class SingularFlagImposedOnMoreThanOneNodeException extends AbstractDomainException
{
    private $flag = '';

    public function getFlag(): string
    {
        return $this->flag;
    }

    public static function fromFlag(string $flag): self
    {
        $self = new self(sprintf('Singular flag "%s" imposed on more than one Node.', $flag));
        $self->flag = $flag;

        return $self;
    }
}
