<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Infrastructure\Mail;

use Tulia\Cms\Shared\Infrastructure\Mail\Exception\MailerConfigurationEmptyException;

/**
 * @author Adam Banaszkiewicz
 */
interface MailerInterface
{
    public function createMessage(string $subject): \Swift_Message;
    public function send(\Swift_Message $message): int;

    /**
     * @throws MailerConfigurationEmptyException
     */
    public function getMailer(): \Swift_Mailer;
}
