<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Infrastructure\Mail;

/**
 * @author Adam Banaszkiewicz
 */
interface MailerConfigurationProviderInterface
{
    public function getFromEmail(): ?string;
    public function getFromName(): ?string;
    public function getHost(): ?string;
    public function getPort(): ?string;
    public function getEncryption(): ?string;
    public function getUsername(): ?string;
    public function getPassword(): ?string;
}
