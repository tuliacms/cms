<?php

declare(strict_types=1);

namespace Tulia\Cms\Settings\Domain\Group;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractSettingsGroup implements SettingsGroupInterface
{
    protected FormFactoryInterface $formFactory;

    abstract public function getId(): string;

    abstract public function getName(): string;

    abstract public function buildView(): array;

    abstract public function buildForm(SettingsStorage $settings): FormInterface;

    public function export(FormInterface $form): array
    {
        return $form->getData();
    }

    public function getIcon(): string
    {
        return 'fa fa-cogs';
    }

    public function getTranslationDomain(): string
    {
        return 'messages';
    }

    public function view(string $view, array $data = []): array
    {
        return [
            'view' => $view,
            'data' => $data,
        ];
    }

    public function createForm(string $type, mixed $data = null, array $options = []): FormInterface
    {
        return $this->formFactory->create($type, $data, $options);
    }

    public function setFormFactory(FormFactoryInterface $formFactory): void
    {
        $this->formFactory = $formFactory;
    }
}
