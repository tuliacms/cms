<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Domain\ReadModel\Model;

use Tulia\Cms\Content\Type\Domain\AbstractModel\AbstractContentType;

/**
 * @author Adam Banaszkiewicz
 */
class ContentType extends AbstractContentType
{
    protected ?string $id;
    protected LayoutType $layout;

    public function __construct(?string $id, string $code, string $type, LayoutType $layout, bool $isInternal)
    {
        $this->id = $id;
        $this->code = $code;
        $this->type = $type;
        $this->layout = $layout;
        $this->isInternal = $isInternal;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return Field[]
     */
    public function getFields(): array
    {
        return parent::getFields();
    }

    public function isInternal(): bool
    {
        return $this->isInternal;
    }

    public function getLayout(): LayoutType
    {
        return $this->layout;
    }

    protected function recordChange(): void
    {
        // void
    }
}
