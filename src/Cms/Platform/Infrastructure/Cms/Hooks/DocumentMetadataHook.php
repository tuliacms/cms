<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Cms\Hooks;

use Tulia\Cms\Platform\Shared\Document\DocumentInterface;
use Tulia\Component\Hooks\HooksSubscriberInterface;

/**
 * @author Adam Banaszkiewicz
 * @final
 * @lazy
 */
class DocumentMetadataHook implements HooksSubscriberInterface
{
    public function __construct(
        private readonly DocumentInterface $document,
    ) {
    }

    public static function getSubscribedActions(): array
    {
        return [
            'theme.head' => 'renderMetatags',
        ];
    }

    public function renderMetatags(): string
    {
        return $this->document->buildMetas().$this->document->buildLinks();
    }
}
