<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Application\UseCase;

use Tulia\Cms\Shared\Application\UseCase\RequestInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class CreateFormRequest implements RequestInterface
{
    public function __construct(
        public readonly string $name,
        public readonly string $subject,
        public readonly array $receivers,
        public readonly string $senderName,
        public readonly string $senderEmail,
        public readonly ?string $replyTo,
        public readonly array $fields,
        public readonly ?string $fieldsTemplate,
        public readonly ?string $messageTemplate,
        public readonly string $websiteId,
        public readonly string $locale,
        public readonly string $defaultLocale,
        public readonly array $localeCodes,
    ) {
    }
}
