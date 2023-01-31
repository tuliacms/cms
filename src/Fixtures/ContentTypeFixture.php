<?php

declare(strict_types=1);

namespace Tulia\Cms\Fixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Tulia\Cms\Content\Type\Domain\WriteModel\ContentTypeRepositoryInterface;
use Tulia\Cms\Content\Type\Domain\WriteModel\Model\ContentType;
use Tulia\Cms\Content\Type\Domain\WriteModel\Rules\CanCreateContentTypeInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class ContentTypeFixture extends Fixture implements FixtureGroupInterface
{
    public function __construct(
        private readonly ContentTypeRepositoryInterface $repository,
        private readonly CanCreateContentTypeInterface $canCreateContentType,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $this->addCategoryTaxonomy();
        $this->addPageNode();
    }

    private function addPageNode(): void
    {
        $type = ContentType::create($this->canCreateContentType, 'page', 'node', 'Page', 'fas fa-boxes', 'simple', false);

        $introduction = uniqid('section_');
        $type->addFieldsGroup($introduction, 'Introduction', 'main', 0);
        $content = uniqid('section_');
        $type->addFieldsGroup($content, 'Content', 'main', 1, true);
        $image = uniqid('section_');
        $type->addFieldsGroup($image, 'Image', 'sidebar', 0);

        $type->addFieldToGroup($introduction, 'introduction', 'textarea', 'Introduction');
        $type->addFieldToGroup($content, 'content', 'tulia_editor', 'Content');
        $type->addFieldToGroup($image, 'thumbnail', 'filepicker', 'Thumbnail');

        $this->repository->insert($type);
    }

    private function addCategoryTaxonomy(): void
    {
        $type = ContentType::create($this->canCreateContentType, 'category', 'taxonomy', 'Category', 'fas fa-folder', 'full-path', true);

        $image = uniqid('section_');
        $type->addFieldsGroup($image, 'Image', 'sidebar', 0);
        $type->addFieldToGroup($image, 'thumbnail', 'filepicker', 'Thumbnail');

        $this->repository->insert($type);
    }

    public static function getGroups(): array
    {
        return ['local-database'];
    }
}
