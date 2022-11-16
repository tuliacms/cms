<?php

declare(strict_types=1);

namespace Tulia\Cms\Seo\Infrastructure\Cms\Content\Attributes;

use Tulia\Cms\Content\Attributes\Domain\ReadModel\Model\AttributesAwareInterface;
use Tulia\Cms\Filemanager\Domain\ReadModel\Service\ImageUrlGeneratorInterface;
use Tulia\Cms\Platform\Shared\Document\DocumentInterface;
use Tulia\Cms\Seo\Domain\Service\SeoDocumentProcessorInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class AttributesAwareSeoDocumentProcessor implements SeoDocumentProcessorInterface
{
    public function __construct(
        private readonly DocumentInterface $document,
        private readonly ImageUrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function aware(AttributesAwareInterface $document, ?string $title = null): void
    {
        $seoTitle = (string) $document->attribute('seo_title');
        $title = $seoTitle ?: $title;
        $description = (string) $document->attribute('seo_description');
        $keywords = (string) $document->attribute('seo_keywords');
        $ogImage = (string) $document->attribute('seo_og_image');
        $robots = $document->attribute('seo_robots');

        $this->document->setTitle($title);
        $this->document->setDescription($description);
        $this->document->setKeywords($keywords);
        $this->document->addMeta('robots', (string) $robots);
        $this->document->addMeta('og:title', $title);
        //$this->document->addMeta('og:url', '....');
        $this->document->addMeta('og:description', $description);
        $this->document->addMeta('og:image', $this->urlGenerator->generate($ogImage));
    }
}
