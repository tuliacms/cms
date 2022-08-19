<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Infrastructure\Mail;

use Tulia\Cms\Shared\Infrastructure\Mail\Exception\MailerConfigurationEmptyException;

/**
 * @author Adam Banaszkiewicz
 */
class Swiftmailer implements MailerInterface
{
    private $mailer;

    public function __construct(
        private MailerConfigurationProviderInterface $config
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function createMessage(string $subject): \Swift_Message
    {
        $message = new \Swift_Message($subject);
        $message->setSender($this->config->getFromEmail(), $this->config->getFromName());

        return $message;
    }

    /**
     * {@inheritdoc}
     */
    public function send(\Swift_Message $message): int
    {
        return $this->createMailer()->send($message);
    }

    public function getMailer(): \Swift_Mailer
    {
        return $this->createMailer();
    }

    private function createMailer(): \Swift_Mailer
    {
        if ($this->mailer instanceof \Swift_Mailer) {
            return $this->mailer;
        }

        return $this->mailer = new \Swift_Mailer($this->createTransport());
    }

    private function createTransport(): \Swift_SmtpTransport
    {
        if (
            null === $this->config->getHost()
            || null === $this->config->getPort()
            || null === $this->config->getEncryption()
        ) {
            throw new MailerConfigurationEmptyException('Host, port or encryption configs are empty. Cannot create mailer transport.');
        }

        $transport = new \Swift_SmtpTransport(
            $this->config->getHost(),
            $this->config->getPort(),
            $this->config->getEncryption()
        );

        if ($this->config->getUsername()) {
            $transport->setUsername($this->config->getUsername());
        }

        if ($this->config->getPassword()) {
            $transport->setUsername($this->config->getPassword());
        }

        return $transport;
    }
}
