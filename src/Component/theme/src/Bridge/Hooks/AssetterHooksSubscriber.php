<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Bridge\Hooks;

use Requtize\Assetter\AssetterInterface;
use Tulia\Cms\Platform\Version;
use Tulia\Component\Hooks\HooksSubscriberInterface;

/**
 * @author Adam Banaszkiewicz
 * @final
 */
class AssetterHooksSubscriber implements HooksSubscriberInterface
{
    public function __construct(
        private readonly AssetterInterface $assetter,
    ) {
    }

    public static function getSubscribedActions(): array
    {
        return [
            'theme.head' => 'registerHeadAssets',
            'theme.body' => 'registerBodyAssets',
        ];
    }

    public function registerHeadAssets(): string
    {
        return $this->assetter->build('head')->all().PHP_EOL.'<meta name="generator" content="Tulia CMS '.Version::VERSION.'" />';
    }

    public function registerBodyAssets(): string
    {
        return $this->assetter->build()->all();
    }
}
