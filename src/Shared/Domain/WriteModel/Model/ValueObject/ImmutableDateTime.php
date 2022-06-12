<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Domain\WriteModel\Model\ValueObject;

use DateTime;
use DateTimeImmutable;

/**
 * @author Adam Banaszkiewicz
 */
final class ImmutableDateTime extends DateTimeImmutable
{
    private DateTimeImmutable $datetime;

    /**
     * {@inheritdoc}
     */
    public function __construct($time = 'now', $timezone = null)
    {
        parent::__construct($time, $timezone);

        $this->datetime = new DateTimeImmutable($time, $timezone);
    }

    /**
     * @throws \Exception
     */
    public static function createFromMutable($object): self|DateTimeImmutable
    {
        $self =  new self();
        $self->datetime = DateTimeImmutable::createFromMutable($object);

        return $self;
    }

    public function sameAs(self $dateTime): bool
    {
        $that = $this->datetime;
        $new  = $dateTime->datetime;

        return $that->getTimestamp() === $new->getTimestamp()
            && $that->getTimezone()->getName() === $new->getTimezone()->getName();
    }

    public function format(string $format): string
    {
        return $this->datetime->format($format);
    }
}
