<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Application\Command;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Filemanager\Application\Command\Helper\FileResponseFormatter;
use Tulia\Cms\Filemanager\Application\UseCase\UploadFile;
use Tulia\Cms\Filemanager\Application\UseCase\UploadFileRequest;
use Tulia\Cms\Filemanager\Domain\ReadModel\Finder\FileFinderInterface;
use Tulia\Cms\Filemanager\Domain\ReadModel\Finder\FileFinderScopeEnum;
use Tulia\Cms\Shared\Application\UseCase\IdResult;

/**
 * @author Adam Banaszkiewicz
 */
class Upload implements CommandInterface
{
    public function __construct(
        private readonly FileFinderInterface $finder,
        private readonly FileResponseFormatter $formatter,
        private readonly UploadFile $uploadFile
    ) {
    }

    public function getName(): string
    {
        return 'upload';
    }

    public function handle(Request $request): array
    {
        $files = [];

        /** @var UploadedFile $file */
        foreach ($request->files as $file) {
            /** @var IdResult $result */
            $result = ($this->uploadFile)(
                new UploadFileRequest(
                    $request->get('directory', DirectoryTree::ROOT),
                    $file->getPathname(),
                    $file->getClientOriginalName()
                )
            );

            $file = $this->finder->findOne(['id' => $result->id], FileFinderScopeEnum::FILEMANAGER);

            $files[] = $this->formatter->format($file);
        }

        return [
            'status' => 'success',
            'uploaded_files' => $files,
        ];
    }
}
