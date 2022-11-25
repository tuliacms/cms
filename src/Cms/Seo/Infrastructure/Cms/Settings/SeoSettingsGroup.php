<?php

declare(strict_types=1);

namespace Tulia\Cms\Seo\Infrastructure\Cms\Settings;

use Symfony\Component\Form\FormInterface;
use Tulia\Cms\Settings\Domain\Group\AbstractSettingsGroup;
use Tulia\Cms\Settings\Domain\Group\SettingsStorage;

/**
 * @author Adam Banaszkiewicz
 * @final
 * @lazy
 */
class SeoSettingsGroup extends AbstractSettingsGroup
{
    public function getId(): string
    {
        return 'seo';
    }

    public function getName(): string
    {
        return 'SEO';
    }

    public function getIcon(): string
    {
        return 'fab fa-searchengin';
    }

    public function getTranslationDomain(): string
    {
        return 'seo';
    }

    public function buildForm(SettingsStorage $settings): FormInterface
    {
        $data = [
            'seo_global_robots' => $settings->get('seo_global_robots', ''),
        ];

        return $this->createForm(SettingsForm::class, $data);
    }

    public function export(FormInterface $form): array
    {
        $data = $form->getData();

        return [
            'seo_global_robots' => $data['seo_global_robots'],
        ];
    }

    public function buildView(): array
    {
        return $this->view('@backend/seo/settings.tpl');
    }
}
