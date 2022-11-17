<?php

declare(strict_types=1);

namespace Tulia\Cms\Settings\UserInterface\Web\Backend\Settings;

use Symfony\Component\Form\FormInterface;
use Tulia\Cms\Settings\Domain\Group\AbstractSettingsGroup;
use Tulia\Cms\Settings\Domain\Group\SettingsStorage;
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

    public function buildForm(SettingsStorage $settings): FormInterface
    {
        return $this->createForm(SettingsForm::class, [
            'administrator_email' => $settings->get('administrator_email'),
            'maintenance_mode'    => $settings->get('maintenance_mode'),
            'maintenance_message' => $settings->get('maintenance_message'),
            'date_format'         => $settings->get('date_format', 'j F, Y'),
            'wysiwyg_editor'      => $settings->get('wysiwyg_editor'),
            'mail_transport'      => $settings->get('mail.transport'),
            'mail_from_email'     => $settings->get('mail.from_email'),
            'mail_from_name'      => $settings->get('mail.from_name'),
            'mail_host'           => $settings->get('mail.host'),
            'mail_port'           => $settings->get('mail.port'),
            'mail_username'       => $settings->get('mail.username'),
            'mail_password'       => $settings->get('mail.password'),
            'mail_encryption'     => $settings->get('mail.encryption'),
            'mail_sendmailpath'   => $settings->get('mail.sendmailpath'),
            'url_suffix'          => $settings->get('url_suffix'),
        ]);
    }

    public function buildView(): array
    {
        return $this->view('@backend/settings/main-settings.tpl');
    }

    public function export(FormInterface $form): array
    {
        $data = $form->getData();

        $result = [
            'administrator_email' => $data['administrator_email'],
            'maintenance_mode' => $data['maintenance_mode'],
            'maintenance_message' => $data['maintenance_message'],
            'date_format' => $data['date_format'],
            'wysiwyg_editor' => $data['wysiwyg_editor'],
            'mail.transport' => $data['mail_transport'],
            'mail.from_email' => $data['mail_from_email'],
            'mail.from_name' => $data['mail_from_name'],
            'mail.host' => $data['mail_host'],
            'mail.port' => $data['mail_port'],
            'mail.username' => $data['mail_username'],
            'mail.password' => $data['mail_password'],
            'mail.encryption' => $data['mail_encryption'],
            'mail.sendmailpath' => $data['mail_sendmailpath'],
            'url_suffix' => $data['url_suffix'],
        ];

        if (!$result['mail.password']) {
            unset($result['mail.password']);
        }

        return $result;
    }
}
