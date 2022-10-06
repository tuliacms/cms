<?php

declare(strict_types=1);

namespace Tulia\Cms\Settings\Domain\Group;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Tulia\Cms\Options\Domain\WriteModel\OptionsRepositoryInterface;
use Tulia\Component\Routing\Website\WebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractSettingsGroup implements SettingsGroupInterface
{
    protected FormFactoryInterface $formFactory;
    protected OptionsRepositoryInterface $options;
    protected WebsiteInterface $website;

    abstract public function getId(): string;

    abstract public function getName(): string;

    abstract public function buildView(): array;

    abstract public function saveAction(array $data): bool;

    abstract public function buildForm(): FormInterface;

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

    public function setOption(string $name, mixed $value): void
    {
        $option = $this->options->get($name, $this->website->getId());
        $option->setValue($value, $this->website->getLocale()->getCode(), $this->website->getDefaultLocale()->getCode());
        $this->options->save($option);
    }

    public function getOption(string $name, mixed $default = null): mixed
    {
        return $this->options->get($name, $this->website->getId())->getValue($this->website->getLocale()->getCode()) ?? $default;
    }

    public function setFormFactory(FormFactoryInterface $formFactory): void
    {
        $this->formFactory = $formFactory;
    }

    public function setOptionsRepository(OptionsRepositoryInterface $options): void
    {
        $this->options = $options;
    }

    public function setWebsite(WebsiteInterface $website): void
    {
        $this->website = $website;
    }
}
