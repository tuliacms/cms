<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Infrastructure\Cms\Importer;

use Tulia\Cms\Filemanager\Application\UseCase\UploadFile;
use Tulia\Cms\Filemanager\Application\UseCase\UploadFileRequest;
use Tulia\Cms\Filemanager\Domain\WriteModel\Model\Directory;
use Tulia\Cms\Shared\Application\UseCase\IdResult;
use Tulia\Component\Importer\ObjectImporter\ObjectImporterInterface;
use Tulia\Component\Importer\Structure\ObjectData;

/**
 * @author Adam Banaszkiewicz
 */
final class FileImporter implements ObjectImporterInterface
{
    public function __construct(
        private readonly UploadFile $uploadFile,
    ) {
    }

    public function import(ObjectData $objectData): ?string
    {
        /** @var IdResult $result */
        $result = ($this->uploadFile)(new UploadFileRequest(
            Directory::ROOT,
            $objectData['filepath'],
        ));

        return $result->id;
    }
}
