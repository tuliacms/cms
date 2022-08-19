<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Domain\WriteModel\NewModel;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use SebastianBergmann\Comparator\RuntimeException;
use Tulia\Cms\ContactForm\Domain\FieldsParser\FieldsParserInterface;
use Tulia\Cms\ContactForm\Domain\WriteModel\Event\ContactFormCreated;
use Tulia\Cms\Shared\Domain\WriteModel\Model\AbstractAggregateRoot;

/**
 * @author Adam Banaszkiewicz
 * @final
 */
class Form extends AbstractAggregateRoot
{
    /** @var ArrayCollection<int, FormTranslation> */
    private Collection $translations;

    private function __construct(
        private string $id,
        private string $name,
        private Sender $sender,
        /** @var string[] $receivers */
        private array $receivers,
        private ?string $replyTo = null,
    ) {
        $this->translations = new ArrayCollection();
    }

    public static function create(
        string $id,
        string $name,
        string $subject,
        string $senderEmail,
        string $senderName,
        array $receivers,
        ?string $replyTo,
        FieldsParserInterface $fieldsParser,
        array $fields,
        ?string $fieldsTemplate,
        ?string $messageTemplate,
        string $locale,
        string $defaultLocale,
        array $localeCodes,
    ): self {
        $self = new self(
            $id,
            $name,
            new Sender($senderEmail, $senderName),
            $receivers,
            $replyTo
        );
        $self->createTranslationsOfNewForm(
            $fieldsParser,
            $fieldsTemplate,
            $fields,
            $messageTemplate,
            $subject,
            $locale,
            $defaultLocale,
            $localeCodes
        );
        $self->recordThat(new ContactFormCreated($self->id));

        return $self;
    }

    private function createTranslationsOfNewForm(
        FieldsParserInterface $fieldsParser,
        ?string $fieldsTemplate,
        array $fields,
        ?string $messageTemplate,
        string $subject,
        string $locale,
        string $defaultLocale,
        array $localeCodes
    ): void {
        $stream = $fieldsParser->parse($fieldsTemplate, $fields);

        foreach ($localeCodes as $code) {
            $translation = new FormTranslation($this, $code);
            $translation->fields = new ArrayCollection(Field::createCollection($translation, $stream->allFields(), $code));
            $translation->messageTemplate = $messageTemplate;
            $translation->fieldsTemplate = $fieldsTemplate;
            $translation->fieldsView = $stream->getResult();
            $translation->subject = $subject;

            if ($code === $locale || $code === $defaultLocale) {
                $translation->translated = true;
            }

            $this->translations->add($translation);
        }
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function toArray(string $locale, string $defaultLocale): array
    {
        if ($this->isTranslatedTo($locale)) {
            $translation = $this->translation($locale);
        } else {
            $translation = $this->translation($defaultLocale);
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'sender_name' => $this->sender->name,
            'sender_email' => $this->sender->email,
            'receivers' => $this->receivers,
            'replyTo' => $this->replyTo,
            'subject' => $translation->subject,
            'message_template' => $translation->messageTemplate,
            'fields_view' => $translation->fieldsView,
            'fields_template' => $translation->fieldsTemplate,
            'translated' => $translation->translated,
            'fields' => $translation->fieldsToArray(),
        ];
    }

    private function isTranslatedTo(string $locale): bool
    {
        foreach ($this->translations->toArray() as $translation) {
            if ($translation->locale === $locale) {
                return true;
            }
        }

        return false;
    }

    private function translation(string $locale): FormTranslation
    {
        foreach ($this->translations->toArray() as $translation) {
            if ($translation->locale === $locale) {
                return $translation;
            }
        }

        throw new \DomainException(sprintf('Cannot find translation for ContactForm for locale %s', $locale));
    }

    public function name(string $name): void
    {
        $this->name = $name;
    }

    public function subject(string $subject, string $locale, string $defaultLocale): void
    {
        $trans = $this->translation($locale);
        $trans->subject = $subject;

        if ($locale === $defaultLocale) {
            foreach ($this->translations as $translation) {
                if (false === $translation->translated) {
                    $translation->subject = $subject;
                }
            }
        }
    }

    public function sender(string $senderEmail, string $senderName): void
    {
        $this->sender = new Sender($senderEmail, $senderName);
    }

    public function replyTo(?string $replyTo): void
    {
        $this->replyTo = $replyTo;
    }

    public function receivers(array $receivers): void
    {
        $this->receivers = $receivers;
    }

    public function fields(
        FieldsParserInterface $fieldsParser,
        array $fields,
        ?string $fieldsTemplate,
        ?string $messageTemplate,
        string $locale,
        string $defaultLocale
    ): void {
        $translation = $this->translation($locale);
        $stream = $fieldsParser->parse($fieldsTemplate, $fields);
        $update = function ($translation) use ($messageTemplate, $fieldsTemplate, $stream, $locale) {
            $translation->messageTemplate = $messageTemplate;
            $translation->fieldsTemplate = $fieldsTemplate;
            $translation->fieldsView = $stream->getResult();
            $translation->fields->clear();

            foreach (Field::createCollection($translation, $stream->allFields(), $locale) as $field) {
                $translation->fields->add($field);
            }
        };

        $update($translation);

        if ($locale === $defaultLocale) {
            foreach ($this->translations as $translation) {
                if (false === $translation->translated) {
                    $update($translation);
                }
            }
        }
    }
}
