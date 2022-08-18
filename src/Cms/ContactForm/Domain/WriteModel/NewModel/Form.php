<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Domain\WriteModel\NewModel;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Tulia\Cms\ContactForm\Domain\WriteModel\Event\FormHasBeenCreated;
use Tulia\Cms\Shared\Domain\WriteModel\Model\AbstractAggregateRoot;

/**
 * @author Adam Banaszkiewicz
 * @final
 */
class Form extends AbstractAggregateRoot
{
    /** @var string[] */
    private array $receivers = [];
    private ?string $replyTo = null;
    /** @var ArrayCollection<int, FormTranslation> */
    private Collection $translations;

    private function __construct(
        private string $id,
        private string $name,
        private Sender $sender
    ) {
        $this->translations = new ArrayCollection();
    }

    public static function create(
        string $id,
        string $name,
        string $senderEmail,
        string $senderName
    ): self {
        $self = new self($id, $name, new Sender($senderEmail, $senderName));
        $self->recordThat(new FormHasBeenCreated($self->id));

        return $self;
    }
}
