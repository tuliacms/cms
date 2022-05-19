<?php

declare(strict_types=1);

namespace Tulia\Cms\SearchAnything\Indexer;

/**
 * @author Adam Banaszkiewicz
 */
final class Document
{
    protected string $title;
    protected string $link;
    protected ?string $description = null;
    protected ?string $image = null;
    protected array $tags = [];

    public function __construct(
        string $title,
        string $link,
        ?string $description = null,
        ?string $image = null,
        array $tags = []
    ) {
        $this->title = $title;
        $this->link = $link;
        $this->description = $description;
        $this->image = $image;
        $this->tags = $tags;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'image' => $this->image,
            'link'  => $this->link,
            'tags'  => $this->tags,
        ];
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function getTags(): array
    {
        return $this->tags;
    }
}
