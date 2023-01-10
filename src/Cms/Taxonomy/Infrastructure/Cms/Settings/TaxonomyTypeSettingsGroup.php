<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Cms\Settings;

use Symfony\Component\Form\FormInterface;
use Tulia\Cms\Content\Type\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\Settings\Domain\Group\AbstractSettingsGroup;
use Tulia\Cms\Settings\Domain\Group\SettingsStorage;

/**
 * @author Adam Banaszkiewicz
 * @final
 * @lazy
 */
class TaxonomyTypeSettingsGroup extends AbstractSettingsGroup
{
    public function __construct(
        private readonly ContentType $contentType,
    ) {
    }

    public function getId(): string
    {
        return 'tulia_taxonomy_'.$this->contentType->getCode();
    }

    public function getName(): string
    {
        return $this->contentType->getName();
    }

    public function getIcon(): string
    {
        return $this->contentType->getIcon();
    }

    public function getTranslationDomain(): string
    {
        return 'taxonomy';
    }

    public function buildForm(SettingsStorage $settings): FormInterface
    {
        $prefixSetting = $this->getSettingPrefix();

        $data = [
            'nodes_per_page' => $settings->get($prefixSetting.'nodes_per_page'),
        ];

        return $this->createForm(SettingsForm::class, $data);
    }

    public function export(FormInterface $form): array
    {
        $prefixSetting = $this->getSettingPrefix();
        $data = $form->getData();

        return [
            $prefixSetting.'nodes_per_page' => $data['nodes_per_page'],
        ];
    }

    public function buildView(): array
    {
        return $this->view('@backend/taxonomy/settings.tpl');
    }

    private function getSettingPrefix(): string
    {
        return sprintf('taxonomy.%s.', $this->contentType->getCode());
    }
}
