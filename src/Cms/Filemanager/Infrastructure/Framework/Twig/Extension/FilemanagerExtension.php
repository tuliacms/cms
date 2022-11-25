<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Infrastructure\Framework\Twig\Extension;

use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Filemanager\Application\Service\ImageUrlResolver;
use Tulia\Cms\Filemanager\Domain\Generator\Html;
use Tulia\Cms\Filemanager\Domain\ImageSize\ImageSizeRegistryInterface;
use Tulia\Cms\Filemanager\Domain\ReadModel\Finder\FileFinderInterface;
use Tulia\Cms\Filemanager\Domain\ReadModel\Finder\FileFinderScopeEnum;
use Tulia\Cms\Filemanager\Domain\ReadModel\Model\File;
use Tulia\Cms\Filemanager\Domain\ReadModel\Service\ImageUrlGeneratorInterface;
use Tulia\Cms\Filemanager\Domain\WriteModel\Model\FileTypeEnum;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class FilemanagerExtension extends AbstractExtension
{
    public function __construct(
        private readonly FileFinderInterface $finder,
        private readonly ImageUrlResolver $urlResolver,
        private readonly ImageUrlGeneratorInterface $urlGenerator,
        private readonly ImageSizeRegistryInterface $imageSizeRegistry,
        private readonly TranslatorInterface $translator,
        private readonly string $publicDir,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('image',        [$this, 'image'],    ['is_safe' => [ 'html' ]]),
            new TwigFunction('image_url',    [$this, 'imageUrl'], ['is_safe' => [ 'html' ]]),
            new TwigFunction('svg',          [$this, 'svg'],      ['is_safe' => [ 'html' ]]),
            new TwigFunction('svg_url',      [$this, 'svgUrl'],   ['is_safe' => [ 'html' ]]),
            new TwigFunction('gallery',      [$this, 'gallery'],  ['is_safe' => [ 'html' ]]),
            new TwigFunction('is_file_type', [$this, 'isFileType'],  ['is_safe' => [ 'html' ]]),
            new TwigFunction('images_sizes', [$this, 'imagesSizes'],  ['is_safe' => [ 'html' ]]),
        ];
    }

    /**
     * Params:
     *     - attributes = List of img tag attributes for HTML
     *     - size = Size name of the image.
     */
    public function image(string $id, array $params = []): string
    {
        $image = $this->finder->findOne([
            'id' => $id,
            'type' => FileTypeEnum::IMAGE,
        ], FileFinderScopeEnum::SINGLE);

        if ($image === null) {
            return '';
        }

        $data = array_merge([
            'alt' => '',
        ], $params['attributes'] ?? []);

        $data['src'] = $this->urlResolver->size(
            $image,
            isset($params['size']) && empty($params['size']) === false
                ? $params['size']
                : 'original'
        );

        return (new Html())->generateImageTag($data);
    }

    /**
     * @param string|File $id
     */
    public function imageUrl($id, array $params = []): string
    {
        return $this->urlGenerator->generate(
            $id instanceof File ? $id->getId() : $id,
            $params
        );
    }

    public function gallery(array $ids, array $params = []): string
    {
        $images = $this->finder->find([
            'id__in' => $ids,
            'type'   => FileTypeEnum::IMAGE,
            'order_by'  => 'id',
            'order_dir' => $ids
        ], FileFinderScopeEnum::SINGLE);

        if ($images->count() === 0) {
            return '';
        }

        $generator = new Html();

        $result = '<div class="tulia-gallery tulia-gallery-type-image">';

        foreach ($images as $image) {
            $result .= '<div class="tulia-gallery-item">' . $generator->generateImageTag([
                'src' => '/' . $image->getPath() . '/' . $image->getFilename()
            ]) . '</div>';
        }

        return $result . '</div>';
    }

    public function svg(string $id, array $params = []): string
    {
        $svg = $this->finder->findOne([
            'id' => $id,
            'type' => FileTypeEnum::SVG,
        ], FileFinderScopeEnum::SINGLE);

        if ($svg === null) {
            return '';
        }

        $data = array_merge([
            'alt' => '',
        ], $params['attributes'] ?? []);

        $data['src'] = '/' . $svg->getPath() . '/' . $svg->getFilename();

        if (isset($params['version']) && empty($params['version']) === false) {
            $data['src'] .= '?version=' . $params['version'];
        }

        return (new Html())->generateImageTag($data);
    }

    public function svgUrl($id): string
    {
        if ($id instanceof File) {
            $svg = $id;
        } else {
            $svg = $this->finder->findOne([
                'id' => $id,
                'type' => FileTypeEnum::SVG,
            ], FileFinderScopeEnum::SINGLE);

            if ($svg === null) {
                return '';
            }
        }

        return '/' . $svg->getPath() . '/' . $svg->getFilename();
    }

    public function isFileType(string $id, string $type): bool
    {
        return (bool) $this->finder->findOne([
            'id' => $id,
            'type' => $type,
        ], FileFinderScopeEnum::SINGLE);
    }

    public function imagesSizes(): array
    {
        return array_map(
            fn($s) => [
                'name' => $s->getName(),
                'label' => $this->translator->trans($s->getLabel(), [], $s->getTranslationDOmain()),
                'width' => $s->getWidth(),
                'height' => $s->getHeight(),
                'mode' => $s->getMode(),
            ],
            $this->imageSizeRegistry->all()
        );
    }
}
