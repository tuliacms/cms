<?php

declare(strict_types=1);

namespace Tulia\Cms\SearchAnything\Domain\WriteModel\Model;

use Tulia\Cms\Shared\Domain\WriteModel\Model\AbstractAggregateRoot;
use Tulia\Cms\Shared\Domain\WriteModel\Model\ValueObject\ImmutableDateTime;

/**
 * @author Adam Banaszkiewicz
 * @final
 */
class Document extends AbstractAggregateRoot
{
    private string $id;
    private ImmutableDateTime $createdAt;
    private ?ImmutableDateTime $updatedAt = null;
    private string $title = '';
    private string $titleSearchable = '';
    private ?string $route = null;
    private array $parameters = [];
    private ?string $description = null;
    private ?string $descriptionSearchable = null;
    private ?string $poster = null;

    public function __construct(
        private readonly string $indexGroup,
        private readonly string $sourceId,
        private readonly string $localizationStrategy,
        private readonly string $multisiteStrategy,
        private readonly ?string $websiteId = null,
        private readonly ?string $locale = null,
    ) {
        $this->createdAt = ImmutableDateTime::now();
    }

    public function setTitle(string $title): void
    {
        $this->title = substr(strip_tags($title), 0, 127);
        $this->titleSearchable = transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $this->title);
        $this->updatedAt = ImmutableDateTime::now();
    }

    public function setLink(string $route, array $parameters = []): void
    {
        $this->route = $route;
        $this->parameters = $parameters;
        $this->updatedAt = ImmutableDateTime::now();
    }

    public function setDescription(?string $description): void
    {
        $this->description = substr(strip_tags($description), 0, 255);
        $this->descriptionSearchable = transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $this->description);
        $this->updatedAt = ImmutableDateTime::now();
    }

    public function setPoster(?string $poster): void
    {
        $this->poster = $poster;
        $this->updatedAt = ImmutableDateTime::now();
    }
}
