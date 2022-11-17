<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Cms\Settings;

use Symfony\Component\Form\FormInterface;
use Tulia\Cms\Settings\Domain\Group\AbstractSettingsGroup;
use Tulia\Cms\Settings\Domain\Group\SettingsStorage;

/**
 * @author Adam Banaszkiewicz
 */
class UsersSettingsGroup extends AbstractSettingsGroup
{
    public function getId(): string
    {
        return 'users';
    }

    public function getName(): string
    {
        return 'users';
    }

    public function getIcon(): string
    {
        return 'fas fa-users';
    }

    public function getTranslationDomain(): string
    {
        return 'users';
    }

    public function buildForm(SettingsStorage $settings): FormInterface
    {
        $data = [
            'password_min_length'        => $settings->get('users.password.min_length', 4),
            'password_min_digits'        => $settings->get('users.password.min_digits', 1),
            'password_min_special_chars' => $settings->get('users.password.min_special_chars', 1),
            'password_min_big_letters'   => $settings->get('users.password.min_big_letters', 1),
            'password_min_small_letters' => $settings->get('users.password.min_small_letters', 1),
            'username_min_length'        => $settings->get('users.username.min_length', 4),
        ];

        return $this->createForm(SettingsForm::class, $data);
    }

    public function export(FormInterface $form): array
    {
        $data = $form->getData();

        return [
            'users.password.min_length' => $data['password_min_length'],
            'users.password.min_digits' => $data['password_min_digits'],
            'users.password.min_special_chars' => $data['password_min_special_chars'],
            'users.password.min_big_letters' => $data['password_min_big_letters'],
            'users.password.min_small_letters' => $data['password_min_small_letters'],
            'users.username.min_length' => $data['username_min_length'],
        ];
    }

    public function buildView(): array
    {
        return $this->view('@backend/user/settings.tpl');
    }
}
