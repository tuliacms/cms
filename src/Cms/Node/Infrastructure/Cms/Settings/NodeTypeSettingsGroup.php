<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Cms\Settings;

use Symfony\Component\Form\FormInterface;
use Tulia\Cms\Content\Type\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\Settings\Domain\Group\AbstractSettingsGroup;
use Tulia\Cms\Settings\Domain\Group\SettingsStorage;

/**
 * @author Adam Banaszkiewicz
 * @final
 * @lazy
 */
class NodeTypeSettingsGroup extends AbstractSettingsGroup
{
    public function __construct(
        private readonly ContentType $contentType,
    ) {
    }

    public function getId(): string
    {
        return 'tulia_node_'.$this->contentType->getCode();
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
        return 'node';
    }

    public function buildForm(SettingsStorage $settings): FormInterface
    {
        $prefixSetting = $this->getSettingPrefix();

        $data = [
            'category_taxonomy' => $settings->get($prefixSetting.'category_taxonomy'),
        ];

        return $this->createForm(SettingsForm::class, $data);
    }

    public function export(FormInterface $form): array
    {
        $prefixSetting = $this->getSettingPrefix();
        $data = $form->getData();

        return [
            $prefixSetting.'category_taxonomy' => $data['category_taxonomy'],
        ];
    }

    public function buildView(): array
    {
        return $this->view('@backend/node/settings.tpl');
    }

    private function getSettingPrefix(): string
    {
        return sprintf('node.%s.', $this->contentType->getCode());
    }
}
