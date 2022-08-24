<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Application\Command;

use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Uid\Uuid;
use Tulia\Cms\Filemanager\Domain\WriteModel\Model\Directory;
use Tulia\Cms\Platform\Shared\ArraySorter;

/**
 * @author Adam Banaszkiewicz
 */
class DirectoryTree implements CommandInterface
{
    public const ROOT = '00000000-0000-0000-0000-000000000000';

    public function __construct(
        private Connection $connection
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'directory-tree';
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request): array
    {
        $open = $request->get('open', self::ROOT);

        $source = $this->connection->fetchAllAssociative('SELECT *, BIN_TO_UUID(id) AS id FROM #__filemanager_directory WHERE id != :root ORDER BY `name`', [
            'root' => Uuid::fromString(Directory::ROOT)->toBinary()
        ]);
        $result = [];

        foreach ($source as $dir) {
            $result[] = [
                'id'          => $dir['id'],
                'text'        => $dir['name'],
                'level'       => $dir['level'] + 1,
                'parent_id'   => $dir['parent_id'] ?: self::ROOT,
                'state'       => [
                    'opened'    => false,
                    'selected'  => $open === $dir['id'],
                ],
                'a_attr' => [
                    'data-id' => $dir['id'],
                    'title'   => $dir['name'],
                ],
            ];
        }

        $result[] = [
            'id'          => self::ROOT,
            'text'        => 'Media',
            'level'       => 0,
            'parent_id'   => '',
            'state'       => [
                'opened'    => true,
                'selected'  => $open !== self::ROOT,
            ],
            'a_attr' => [
                'data-id' => self::ROOT,
                'title'   => 'Media',
            ],
        ];

        return (new ArraySorter($result, [ 'flat_result' => false ]))->sort();
    }
}
