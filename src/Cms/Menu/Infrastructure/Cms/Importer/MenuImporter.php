<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Cms\Importer;

use Tulia\Cms\Menu\Application\UseCase\CreateMenu;
use Tulia\Cms\Menu\Application\UseCase\CreateMenuItem;
use Tulia\Cms\Menu\Application\UseCase\CreateMenuItemRequest;
use Tulia\Cms\Menu\Application\UseCase\CreateMenuRequest;
use Tulia\Cms\Shared\Application\UseCase\IdResult;
use Tulia\Component\Importer\ObjectImporter\ObjectImporterInterface;
use Tulia\Component\Importer\ObjectImporter\Traits\WebsiteAwareTrait;
use Tulia\Component\Importer\Structure\ObjectData;

/**
 * @author Adam Banaszkiewicz
 */
class MenuImporter implements ObjectImporterInterface
{
    use WebsiteAwareTrait;

    public function __construct(
        private readonly CreateMenu $createMenu,
        private readonly CreateMenuItem $createMenuItem,
    ) {
    }

    public function import(ObjectData $objectData): ?string
    {
        /** @var IdResult $id */
        $id = ($this->createMenu)(new CreateMenuRequest(
            $objectData['name'],
            $this->getWebsite()->getId(),
        ));

        foreach ($objectData['items'] as $item) {
            $this->importItem($id->id, $item);
        }

        return $id->id;
    }

    private function importItem(string $menuId, ObjectData $item, ?string $parent = null): void
    {
        $details = [
            'name' => $item['name'],
            'type' => $item['link_type'],
            'identity' => $item['link_identity'] ?? null,
            'hash' => $item['hash'] ?? '',
            'target' => $item['target'] ?? '_self',
            'parent' => $parent ?? null,
        ];

        /** @var IdResult $id */
        $id = ($this->createMenuItem)(new CreateMenuItemRequest(
            $menuId,
            $details,
            $this->getWebsite()->getLocale()->getCode(),
            $this->getWebsite()->getLocaleCodes()
        ));

        foreach ($item['children'] as $child) {
            $this->importItem($menuId, $child, $id->id);
        }
    }
}
