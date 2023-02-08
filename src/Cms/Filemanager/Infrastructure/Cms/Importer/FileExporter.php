<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Infrastructure\Cms\Importer;

use Symfony\Component\Filesystem\Filesystem;
use Tulia\Cms\Filemanager\Domain\ReadModel\Finder\FileFinderInterface;
use Tulia\Cms\Filemanager\Domain\ReadModel\Finder\FileFinderScopeEnum;
use Tulia\Component\Importer\ObjectExporter\ObjectExporterInterface;
use Tulia\Component\Importer\ObjectExporter\ObjectsCollection\ObjectsCollection;
use Tulia\Component\Importer\ObjectExporter\ObjectsCollection\ObjectToExport;
use Tulia\Component\Importer\ObjectExporter\Traits\WebsiteAwareTrait;
use Tulia\Component\Importer\Structure\ObjectData;

/**
 * @author Adam Banaszkiewicz
 */
final class FileExporter implements ObjectExporterInterface
{
    use WebsiteAwareTrait;

    public function __construct(
        private readonly FileFinderInterface $fileFinder,
        private readonly string $publicDirectory,
        private readonly Filesystem $filesystem,
    ) {
    }

    public function collectObjects(ObjectsCollection $collection): void
    {
        /**
         * @todo After refactor filemanager this finder should be replaces with dedicated DBAL query.
         */
        $files = $this->fileFinder->find([
            'website_id' => $this->getWebsite()->getId(),
            'locale' => $this->getWebsite()->getLocale()->getCode(),
        ], FileFinderScopeEnum::FILEMANAGER);

        foreach ($files as $file) {
            $collection->addObject(new ObjectToExport('File', $file->getId(), $file->getFilename()));
        }
    }

    public function export(ObjectData $objectData): void
    {
        $file = $this->fileFinder->findOne([
            'website_id' => $this->getWebsite()->getId(),
            'locale' => $this->getWebsite()->getLocale()->getCode(),
            'id' => $objectData->getObjectId(),
        ], FileFinderScopeEnum::FILEMANAGER);

        $this->filesystem->copy(
            $this->publicDirectory.$file->getPath().'/'.$file->getFilename(),
            $objectData->getImportRootPath().'/'.$file->getFilename(),
        );

        $objectData['filepath'] = './'.$file->getFilename();
    }
}
