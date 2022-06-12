<?php

declare(strict_types=1);

namespace Tulia\Cms\Options\Infrastructure\Mailer;

use Tulia\Cms\Shared\Infrastructure\Mail\MailerConfigurationProviderInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class OptionsAwareMailerConfigurationProvider implements MailerConfigurationProviderInterface
{
    private bool $preloaded = false;

    public function __construct(
        private Options $options
    ) {
    }

    public function getFromEmail(): string
    {
        $this->preload();
        return $this->options->get('mail.from_email');
    }

    public function getFromName(): string
    {
        $this->preload();
        return $this->options->get('mail.from_name');
    }

    public function getHost(): string
    {
        $this->preload();
        return $this->options->get('mail.host');
    }

    public function getPort(): string
    {
        $this->preload();
        return $this->options->get('mail.port');
    }

    public function getEncryption(): string
    {
        $this->preload();
        return $this->options->get('mail.encryption');
    }

    public function getUsername(): string
    {
        $this->preload();
        return $this->options->get('mail.username');
    }

    public function getPassword(): string
    {
        $this->preload();
        return $this->options->get('mail.password');
    }

    private function preload(): void
    {
        if ($this->preloaded) {
            return;
        }

        $this->options->preload([
            'mail.from_email',
            'mail.from_name',
            'mail.host',
            'mail.port',
            'mail.encryption',
            'mail.username',
            'mail.password',
        ]);

        $this->preloaded = true;
    }
}
