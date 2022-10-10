<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Builder\Structure;

/**
 * @author Adam Banaszkiewicz
 */
class Section
{
    private string $code;
    private array $controls = [];
    private ?string $label = null;
    private ?string $parent = null;
    private string $transationDomain = 'messages';
    private ?string $description = null;

    public function __construct(string $code, string $label, array $controls = [], ?string $parent = null)
    {
        $this->code = $code;
        $this->label = $label;
        $this->parent = $parent;

        foreach ($controls as $control) {
            $this->controls[$control['code']] = Control::fromArray($control);
        }
    }

    public static function fromArray(array $data): self
    {
        $self = new self($data['code'], (string) $data['label'], $data['controls'], $data['parent']);
        $self->transationDomain = $data['translation_domain'];
        $self->description = $data['description'];

        return $self;
    }

    public function merge(self $section): void
    {
        if ($section->label) {
            $this->label = $section->label ?? $this->label;
        }
        if ($section->label) {
            $this->parent = $section->parent ?? $this->parent;
        }

        /** @var Control $control */
        foreach ($section->controls as $control) {
            if (isset($this->controls[$control->getCode()])) {
                $this->controls[$control->getCode()]->merge($control);
            } else {
                $this->controls[$control->getCode()] = $control;
            }
        }
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    /**
     * @return Control[]
     */
    public function getControls(): array
    {
        return $this->controls;
    }

    public function setControls(array $controls): void
    {
        $this->controls = $controls;
    }

    public function getParent(): ?string
    {
        return $this->parent;
    }

    public function getTransationDomain(): string
    {
        return $this->transationDomain;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}
