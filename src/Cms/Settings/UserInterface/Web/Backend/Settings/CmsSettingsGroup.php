<?php

declare(strict_types=1);

namespace Tulia\Cms\Settings\UserInterface\Web\Backend\Settings;

use Symfony\Component\Form\FormInterface;
use Tulia\Cms\Settings\Domain\Group\AbstractSettingsGroup;
use Tulia\Cms\Settings\UserInterface\Web\Backend\Form\SettingsForm;

/**
 * @author Adam Banaszkiewicz
 */
class CmsSettingsGroup extends AbstractSettingsGroup
{
    public function getId(): string
    {
        return 'cms';
    }

    public function getName(): string
    {
        return 'settings';
    }

    public function buildForm(): FormInterface
    {
        $data = [
            'administrator_email' => $this->getOption('administrator_email'),
            'maintenance_mode'    => $this->getOption('maintenance_mode'),
            'maintenance_message' => $this->getOption('maintenance_message'),
            'date_format'         => $this->getOption('date_format', 'j F, Y'),
            'wysiwyg_editor'      => $this->getOption('wysiwyg_editor'),
            'mail_transport'      => $this->getOption('mail.transport'),
            'mail_from_email'     => $this->getOption('mail.from_email'),
            'mail_from_name'      => $this->getOption('mail.from_name'),
            'mail_host'           => $this->getOption('mail.host'),
            'mail_port'           => $this->getOption('mail.port'),
            'mail_username'       => $this->getOption('mail.username'),
            'mail_password'       => $this->getOption('mail.password'),
            'mail_encryption'     => $this->getOption('mail.encryption'),
            'mail_sendmailpath'   => $this->getOption('mail.sendmailpath'),
            'url_suffix'          => $this->getOption('url_suffix'),
        ];

        return $this->createForm(SettingsForm::class, $data);
    }

    public function buildView(): array
    {
        return $this->view('@backend/settings/main-settings.tpl');
    }

    public function saveAction(array $data): bool
    {
        $this->setOption('administrator_email', $data['administrator_email']);
        $this->setOption('maintenance_mode', $data['maintenance_mode']);
        $this->setOption('maintenance_message', $data['maintenance_message']);
        $this->setOption('date_format', $data['date_format']);
        $this->setOption('wysiwyg_editor', $data['wysiwyg_editor']);
        $this->setOption('mail.transport', $data['mail_transport']);
        $this->setOption('mail.from_email', $data['mail_from_email']);
        $this->setOption('mail.from_name', $data['mail_from_name']);
        $this->setOption('mail.host', $data['mail_host']);
        $this->setOption('mail.port', $data['mail_port']);
        $this->setOption('mail.username', $data['mail_username']);

        if ($data['mail_password']) {
            $this->setOption('mail.password', $data['mail_password']);
        }

        $this->setOption('mail.encryption', $data['mail_encryption']);
        $this->setOption('mail.sendmailpath', $data['mail_sendmailpath']);
        $this->setOption('url_suffix', $data['url_suffix']);

        return true;
    }
}
