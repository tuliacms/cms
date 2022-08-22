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

    /**
     * {@inheritdoc}
     */
    abstract public function getId(): string;

    /**
     * {@inheritdoc}
     */
    abstract public function getName(): string;

    /**
     * {@inheritdoc}
     */
    abstract public function buildView(): array;

    /**
     * {@inheritdoc}
     */
    abstract public function saveAction(array $data): bool;

    /**
     * {@inheritdoc}
     */
    abstract public function buildForm(): FormInterface;

    /**
     * {@inheritdoc}
     */
    public function getIcon(): string
    {
        return 'fa fa-cogs';
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslationDomain(): string
    {
        return 'messages';
    }

    /**
     * {@inheritdoc}
     */
    public function view(string $view, array $data = [])
    {
        return [
            'view' => $view,
            'data' => $data,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function createForm(string $type, $data = null, array $options = []): FormInterface
    {
        return $this->formFactory->create($type, $data, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function setOption(string $name, $value): void
    {
        $option = $this->options->get($name, $this->website->getId());
        $option->setValue($value, $this->website->getLocale()->getCode(), $this->website->getDefaultLocale()->getCode());
        $this->options->save($option);
    }

    /**
     * {@inheritdoc}
     */
    public function getOption(string $name, $default = null)
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
