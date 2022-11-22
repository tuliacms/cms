<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Shared\Document;

/**
 * @author Adam Banaszkiewicz
 */
class Document implements DocumentInterface
{
    protected $title;
    protected $description;
    protected $keywords;
    protected $attributes = [];
    protected $links = [];
    protected $metas = [];

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setKeywords(?string $keywords): void
    {
        $this->keywords = $keywords;
    }

    public function getKeywords(): ?string
    {
        return $this->keywords;
    }

    public function setAttribute(?string $name, ?string $value): void
    {
        $this->attributes[$name] = $value;
    }

    public function getAttribute(?string $name, ?string $default = null)
    {
        return $this->attributes[$name] ?? $default;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    public function addLink(array $attributes): void
    {
        $this->links[] = $attributes;
    }

    public function getLinks(): array
    {
        return $this->links;
    }

    public function setLinks(array $links): void
    {
        $this->links = $links;
    }

    public function buildLinks(): string
    {
        $links = $this->links;

        $result = [];

        foreach ($links as $link) {
            $result[] = '<link '.$this->buildAttributes($link).' />';
        }

        return implode(PHP_EOL, $result);
    }

    public function addMeta(string $name, string $value): void
    {
        $this->metas[] = [
            'name'  => $name,
            'content' => $value,
        ];
    }

    public function getMetas(): array
    {
        return $this->metas;
    }

    public function setMetas(array $metas): void
    {
        $this->metas = $metas;
    }

    public function buildMetas(): string
    {
        $metas = $this->metas;

        $metas[] = [
            'name'  => 'keywords',
            'content' => $this->keywords
        ];
        $metas[] = [
            'name'  => 'description',
            'content' => $this->description
        ];

        $result = [];

        if ($this->title) {
            $result[] = '<title>' . $this->title . '</title>';
        }

        foreach ($metas as $meta) {
            if ($meta['content']) {
                $result[] = '<meta ' . $this->buildAttributes($meta) . ' />';
            }
        }

        return implode(PHP_EOL, $result);
    }

    private function buildAttributes(array $attrs): string
    {
        $prepared = [];

        foreach ($attrs as $name => $val) {
            $prepared[] = $name.'="'.($val ? htmlspecialchars($val, ENT_QUOTES) : null).'"';
        }

        return implode(' ', $prepared);
    }
}
