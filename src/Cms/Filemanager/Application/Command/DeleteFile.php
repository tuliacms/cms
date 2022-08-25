<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Application\Command;

use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Filemanager\Application\UseCase\DeleteFile as DeleteFileUseCase;
use Tulia\Cms\Filemanager\Application\UseCase\DeleteFileRequest;

/**
 * @author Adam Banaszkiewicz
 */
final class DeleteFile implements CommandInterface
{
    public function __construct(
        private readonly DeleteFileUseCase $uploadFile,
    ) {
    }

    public function getName(): string
    {
        return 'delete';
    }

    public function handle(Request $request): array
    {
        $ids = explode(',', $request->query->get('id'));
        $ids = array_filter($ids);
        ($this->uploadFile)(new DeleteFileRequest($ids));

        return [];
    }
}
